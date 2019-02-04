<?php 

require_once 'ImgCompressor.php';

class CompressImg 
{  
    protected $options;

    public function __construct ($options = array()) {
        $this->options = $options;
    }

    public function compressImgFilter ($img, $quality = 0) {
        $projectDir = $this->getProjectDir(); 
        $imgDir = $this->getImgDir ($img);
        // setting
        $setting = array(
            'directory' => $imgDir, // directory file compressed output
            'file_type' => array( // file format allowed
                'image/jpeg',
                'image/png',
                'image/gif'
            )
        );
        // create object
        $ImgCompressor = new ImgCompressor($setting);
        
        $my_img = $img;
        // get extension
        $im_ex = explode('/', $img);
        $im_ex = end($im_ex); 
        $im_ex = explode('.', $im_ex);  
        // $im_name = ($my_img).'.'. $im_ex;
        $im_name = $im_ex[0] .'.'. $im_ex[1];
        $im_ex = $im_ex[1];
        // compressed img
        $my_compressed_img = $imgDir . '/' . $im_name . $im_ex;
       
        // create if it doesn't exist
        if (file_exists($my_img)) {
            $my_compressed_img = $ImgCompressor->run($my_img, 'jpg', $quality);
            if ($my_compressed_img['status'] == 'success') {  
                $compressed_img_name = $my_compressed_img['data']['compressed'];
                $compressed_img_name = $compressed_img_name['name'];
                echo '<p> success </p>';
            } else {
                echo '<p> failure </p>';
                // un nom de fichier encryté qui n'existe pas
                $compressed_img_name = $im_name;
            }
        } 

        // si l'image existe afficher l'image compressée sinon afficher l'image originale
        $img_to_display = (file_exists($my_img)) ? $imgDir . '/' . $im_name : $img;
        echo "<p>compressed to : $img_to_display</p>";
    	return $img_to_display;
    }

    private function getImgDir ($img) { 
        if (!is_string($img)) {
            var_dump($img, 'getimgdir');die;
        }
        $img_exploded = explode('/', $img);
        $removed = array_pop($img_exploded);

        $original_dir = implode('/', $img_exploded);
        $compressed_dir = str_replace($this->options['project_dir'] . '/' . $this->options['original_dirname'], $this->options['project_dir'] . '/' . $this->options['compressed_dirname'], $original_dir);
        return $compressed_dir;
    }

    public function getProjectDir () {
        $projectDir = __DIR__ . '/compressed' ;
        return $projectDir;
    } 
}
