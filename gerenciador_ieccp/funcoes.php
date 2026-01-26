<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    function compress($source, $destination) {
        try {
            \Tinify\setKey("s4nx9Zcwjsvpz7vk9XzJv1TdvzqfKYZC");
            $sourceData = \Tinify\fromFile($source);
            $resized = $sourceData->resize(array(
                "method" => "scale",
                "width" => 800
            ));

            $resized->toFile($destination);

            return true;
        } catch (\Tinify\Exception $e) {
            error_log("Erro TinyPNG: " . $e->getMessage());
            return false;
        }
    }
?>