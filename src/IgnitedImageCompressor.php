<?php

namespace IgnitedDevPackage;

class IgnitedImageCompressor
{
    public function ignitedCompressAndResizeImage($file, $destination, $maxImgWidth, $quality)
    {
        // Get image information
        $info = getimagesize($file['tmp_name']);
        $mime = $info['mime'];

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($file['tmp_name']);
                $save_function = 'imagejpeg';
                break;
            case 'image/png':
                $src = imagecreatefrompng($file['tmp_name']);
                $save_function = 'imagepng';
                break;
            case 'image/gif':
                $src = imagecreatefromgif($file['tmp_name']);
                $save_function = 'imagegif';
                break;
            case 'image/webp':
                $src = imagecreatefromwebp($file['tmp_name']);
                $save_function = 'imagewebp';
                break;
            case 'image/avif':
                $src = imagecreatefromavif($file['tmp_name']);
                $save_function = 'imageavif';
                break;
            default:
                $save_function = '';
        }

        if (!empty($save_function)) {
            // Get the original dimensions of the uploaded image
            list($width, $height) = getimagesize($file['tmp_name']);

            // If resizing is needed
            if ($width > $maxImgWidth) {
                // Calculate the new dimensions maintaining the aspect ratio
                $newwidth = $maxImgWidth;
                $newheight = (int)(($height / $width) * $newwidth);

                // Create a new image with the new dimensions
                $newImage = imagecreatetruecolor($newwidth, $newheight);

                // Preserve transparency for PNG, GIF, and WebP
                if ($mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/webp') {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                }

                // Resample the image to the new size
                imagecopyresampled($newImage, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

                // Ensure destination directory exists, if not, create it
                $destination_dir = dirname($destination);
                if (!is_dir($destination_dir)) {
                    mkdir($destination_dir, 0755, true); // 0755 permissions and recursive creation
                }

                // Save the resized and compressed image
                if ($mime == 'image/jpeg') {
                    // For JPEG, use quality 0-100
                    $save_function($newImage, $destination, $quality);
                } elseif ($mime == 'image/png') {
                    // For PNG, use compression level 0-9 (quality 0-9)
                    $save_function($newImage, $destination, floor($quality / 10)); // Compression level
                } elseif ($mime == 'image/gif' || $mime == 'image/webp' || $mime == 'image/avif') {
                    // For GIF, WebP, and AVIF, use the default compression method (without quality level)
                    $save_function($newImage, $destination);
                }

                // Free up memory
                imagedestroy($src);
                imagedestroy($newImage);

                // Prepare response data in JSON format
                $response = array(
                    'status' => 'success',
                    'message' => 'Image resized and compressed successfully.',
                    'original_size' => $this->ignited_convert_filesize(filesize($file['tmp_name'])),
                    'compressed_size' => $this->ignited_convert_filesize(filesize($destination)),
                    'destination' => $destination,
                );

                // Return the response as a JSON object
                return json_encode($response);
            }
        }

        // Prepare failure response if no resizing was needed
        $response = array(
            'status' => 'failure',
            'message' => 'No resizing needed for the image.',
        );

        // Return failure response
        return $response;
    }

    // Function to convert file size to human-readable format
    protected function ignited_convert_filesize($bytes, $decimals = 2) {
        $size = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
