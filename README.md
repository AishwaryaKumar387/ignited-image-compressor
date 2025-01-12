# ignited-image-compressor
ignited-image-compressor is a lightweight PHP package that provides image compression and resizing functionality. 

        // Include the Composer autoloader
        require_once __DIR__ . '/vendor/autoload.php';

        // Use the correct namespace for your package
        use IgnitedDevPackage\IgnitedImageCompressor;

        // Path to the test image file
        $filePath = 'ryan-plomp-tFPgk_FNEzM-unsplash.png';  // Path to your image file

        // Check if the file exists
        if (file_exists($filePath)) {
            // Manually set the file array
            $file = array(
                'name' => basename($filePath), // Get the file name
                'tmp_name' => $filePath,       // Get the full path to the file
                'size' => filesize($filePath)  // Get the file size
            );

            // Set destination directory for compressed images
            $destination_dir = __DIR__ . '/compressed-images';

            // Ensure the destination directory exists
            if (!is_dir($destination_dir)) {
                mkdir($destination_dir, 0755, true); // Create directory if it doesn't exist
            }

            // Create destination image path with a unique name i.e. adjust with your file name
            $destination_image = $destination_dir . '/' . pathinfo($file['name'], PATHINFO_FILENAME) . '_compressed.' . pathinfo($file['name'], PATHINFO_EXTENSION);

            // Maximum width for resizing and quality level for JPEG
            $maxImgWidth = 900;
            $quality = 40;  // Quality for JPEG (0-100), PNG, WebP, and AVIF use a level (0-9)

            // Create an instance of the image compressor
            $compressor = new IgnitedImageCompressor();

            // Call the method to compress and resize the image
            $result = $compressor->ignitedCompressAndResizeImage($file, $destination_image, $maxImgWidth, $quality);

            // Output the result
            print_r($result);
        } else {
            print_r(array('status' => 'error', 'message' => 'The specified file does not exist.'));
        }

