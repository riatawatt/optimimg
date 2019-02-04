<?php

namespace Softivendor\MainBundle\Twig\Extension;

use Softivendor\MainBundle\Twig\Extension\ImgCompressor;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Softivendor\MainBundle\Twig\Extension\Resize;

class CompressImgExtension extends \Twig_Extension
{

    protected $container;

    public function __construct(ContainerInterface $container) 
    {
        $this->container = $container;
    }

	public function getFilters()
    {
        // compress : compression à 50 % (5) ,max_compress: compression à maximale (9)
        return array(
            new \Twig_SimpleFilter('compress', array($this, 'compressImgFilter')),
            new \Twig_SimpleFilter('max_compress', array($this, 'max_compressImgFilter')),
            new \Twig_SimpleFilter('resize', array($this, 'resize'))
        );
    }

    /**
     *  resize sans crop en maintenant le ratio
     *  $width et $heigth sont les dimensions de resize. les dimensions finales sont supérieurs ou égale
     *  $quality entre 0 mauvais et 100 très bon
     */
    public function resize ($img, $width, $height, $quality) {
        $projectDir = $this->getProjectDir();
        $imgDir = $projectDir. '/web/img';
        $setting = array(
            'directory' => $imgDir.'/compressed',
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
        );

        $im_ex = explode('.', $img);
        $im_ex = end($im_ex);
		//$im_ex = "villa".$width."".$im_ex;

        // pour env app et app_dev
        if (substr($img, 0, 4) != '/web' ) {
            $path = $projectDir. '/web' .$img;
        } else {
            $path = $projectDir .$img;
        }

        
        $im_name = md5($path).'villa'.$width.'.'.$im_ex;

        // return if already exist
        if (file_exists($setting['directory']."/".$im_name)) {
            return "/web/img/compressed/".$im_name;
        }

        // resize and create if don't exist yet
        $to = $setting['directory'].'/'.$im_name;
        $width = $setting['width'];
        $height = $setting['height'];
        $res_func = new Resize($setting);
        $resize_success = (file_exists($path)) ? $res_func->resize($path, $to, $width, $height, true, $im_name) : $img;

        return ($resize_success) ? "/web/img/compressed/".$im_name : $img;
    }

    public function compressImgFilter ($img, $quality = 0) {
        $projectDir = $this->getProjectDir();
        $imgDir = $projectDir. '/web/img';
        // setting
        $setting = array(
            'directory' => $imgDir.'/compressed', // directory file compressed output
            'file_type' => array( // file format allowed
                'image/jpeg',
                'image/png',
                'image/gif'
            )
        );
        // create object
        $ImgCompressor = new ImgCompressor($setting);
        
        $my_img = $projectDir. '/web' .$img;
        // get extension
        $im_ex = explode('.', $img);
        $im_ex = end($im_ex);
        $im_name = md5($my_img).'.'. $im_ex;
        // compressed img
        $my_compressed_img = $imgDir. '/compressed/'.$im_name;
        // create if it doesn't exist
        if (!file_exists($my_compressed_img) && file_exists($my_img)) {
            $my_compressed_img = $ImgCompressor->run($my_img, 'jpg', $quality);
            if ($my_compressed_img['status'] == 'success') {
                $compressed_img_name = $my_compressed_img['data']['compressed'];
                $compressed_img_name = $compressed_img_name['name'];
            } else {
                // un nom de fichier encryté qui n'existe pas
                $compressed_img_name = $im_name;
            }
        } else {
            // reprendre le fichier car déjà généré
            $compressed_img_name = $im_name;
        }

        // si l'image existe afficher l'image compressée sinon afficher l'image originale
        $img_to_display = (file_exists($my_img)) ? '/web/img/compressed/'. $compressed_img_name : '/web' .$img;

    	return $img_to_display;
    }

    public function max_compressImgFilter ($img, $quality = 9) {
        $projectDir = $this->getProjectDir();
        $imgDir = $projectDir. '/web/img';
        // setting
        $setting = array(
            'directory' => $imgDir.'/compressed', // directory file compressed output
            'file_type' => array( // file format allowed
                'image/jpeg',
                'image/png',
                'image/gif'
            )
        );
        // create object
        $ImgCompressor = new ImgCompressor($setting);
        
        $my_img = $projectDir. '/web' .$img;
        // get extension
        $im_ex = explode('.', $img);
        $im_ex = end($im_ex);
        $im_name = md5($my_img).'.'. $im_ex;
        // compressed img
        $my_compressed_img = $imgDir. '/compressed/'.$im_name;
        // create if it doesn't exist
        if (!file_exists($my_compressed_img) && file_exists($my_img)) {
            $my_compressed_img = $ImgCompressor->run($my_img, 'jpg', 9);
            $compressed_img_name = $my_compressed_img['data']['compressed'];
            $compressed_img_name = $compressed_img_name['name'];
        } else {
            $compressed_img_name = $im_name;
        }

        $img_to_display = (file_exists($my_img)) ? '/web/img/compressed/'. $compressed_img_name : '/web' .$img;

        return $img_to_display;
    }
	
	/**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'compress_img_extension';
    }

    public function getProjectDir () {
        $rootDir = $this->container->get('kernel')->getRootDir();
        $app_string = substr($rootDir, -4);
        $projectDir = str_replace($app_string, "", $rootDir);
        return $projectDir;
    }

    

}
