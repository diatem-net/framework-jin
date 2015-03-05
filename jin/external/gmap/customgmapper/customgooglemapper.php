<?php
/**
 * Framework JIN
 */
namespace jin\external\gmap\customgmapper;

use jin\external\gmap\GeoProjectionMercator;
use jin\external\gmap\customgmapper\GeoZone;
use jin\external\gmap\customgmapper\GeoPoint;
use jin\filesystem\Folder;
use jin\image\Image;
use jin\image\filters\ImageImport;
use jin\image\ImagePart;
use jin\lang\StringTools;
use jin\lang\NumberTools;
use jin\image\filters\AbsoluteResize;
use jin\image\filters\Opacity;
use jin\image\filters\RectangleFill;

/**
 * Classe permettant le mapping d'une image en tuiles GoogleMap
 */
class CustomGoogleMapper{
    /**
     * Niveau de zoom
     * @var integer
     */
    private $zoom;
    
    /**
     * Chemin de l'image à mapper
     * @var string
     */
    private $imagePath;
    
    /**
     * Représentation géographique des coordonnées de l'image
     * @var \jin\external\gmap\customgmapper\GeoZone
     */
    private $imageGeoZone;
    
    
     /**
     * Représentation géographique de la zone valide. (le reste devant être masqué). Si Null pas de masquage
     * @var \jin\external\gmap\customgmapper\GeoZone
     */
    private $valideGeoZone;
    
    /**
     * Couleur de masquage (en dehors de la zone valide). Composante rouge.
     * @var integer
     */
    private $masquageColorR;
    
     /**
     * Couleur de masquage (en dehors de la zone valide). Composante verte.
     * @var integer
     */
    private $masquageColorG;
    
     /**
     * Couleur de masquage (en dehors de la zone valide). Composante bleue.
     * @var integer
     */
    private $masquageColorB;
    
    /**
     * Représentation géographique des coordonnées des tuiles recouvertes par l'image au niveau de zoom spécifié
     * @var \jin\external\gmap\customgmapper\GeoZone
     */
    private $tilesGeoZone;
    
    /**
     * Tuile SudOuest {'x' => integer, 'y' => integer}
     * @var array   
     */
    private $soTile;
    
     /**
     * Infos tuile SudOuest {'min' => {'lat' => integer, 'long' => integer}, 'max' => {'lat' => integer, 'long' => integer}}
     * @var array   
     */
    private $soTileInfo;
    
     /**
     * Tuile NordEst {'x' => integer, 'y' => integer}
     * @var array   
     */
    private $neTile;
    
    /**
     * Infos tuile NoedEst {'min' => {'lat' => integer, 'long' => integer}, 'max' => {'lat' => integer, 'long' => integer}}
     * @var array   
     */
    private $neTileInfo;
    
    /**
     * Largeur de l'image de sortie avant découpe
     * @var integer
     */
    private $outputWidth;
    
    /**
     * Hauteur de l'image de sortie avant découpe
     * @var integer
     */
    private $outputHeight;
    
    /**
     * Nombre de tuiles en largeur
     * @var integer
     */
    private $nbTilesX;
    
    /**
     * Nombre de tuiles en hauteur
     * @var integer
     */
    private $nbTilesY;
    
    /**
     * Taille h/w de la tuile de sortie en pixel (256 pour GoogleMap)
     * @var type 
     */
    private $tileZize = 256;
    
    /**
     * Objet GeoProjectionMercator
     * @var \jin\external\gmap\GeoProjectionMercator
     */
    private static $gpm;
    
    
    /**
     * Constructeur
     * @param integer           $zoom       Niveau de zoom
     * @param string            $imagePath  Chemin de l'image à mapper
     * @param integer           $lat1       Latitude du point A de l'image     
     * @param integer           $lat2       Latitude du point B de l'image
     * @param integer           $lon1       Longitude du point A de l'image
     * @param integer           $lon2       Longitude du point B de l'image
     */
    public function __construct($zoom, $imagePath, $lat1, $lat2, $lon1, $lon2) {
        $this->zoom = $zoom;
        $this->imagePath = $imagePath;
        $this->imageGeoZone = new GeoZone($lat1, $lat2, $lon1, $lon2);
        
        self::$gpm = new GeoProjectionMercator($this->tileZize);
        
        $this->calculateLimitTiles();
        $this->calculateOutputSize();
    }
    
    
    /**
     * Définit une zone de masquage. On remplit de la couleur indiquée les zones situées en dehors de la zone indiquée.
     * @param integer $lat1     Latitude du point A de la zone "valide"
     * @param integer $lat2     Latitude du point B de la zone "valide"
     * @param integer $lon1     Longitude du point A de la zone "valide"
     * @param integer $lon2     Longitude du point B de la zone "valide"
     * @param integer $r        Couleur de remplissage. Composante rouge.
     * @param integer $g        Couleur de remplissage. Composante verte.
     * @param integer $b        Couleur de remplissage. Composante bleue.
     */
    public function setMaskZone($lat1, $lat2, $lon1, $lon2, $r, $g, $b){
        $this->valideGeoZone = new GeoZone($lat1, $lat2, $lon1, $lon2);
        $this->masquageColorR = $r;
        $this->masquageColorG = $g;
        $this->masquageColorB = $b;
    }
    
    
    /**
     * Calcule les emplacements des tuiles limites SUDOUEST et NORDEST
     */
    private function calculateLimitTiles(){
        $so = $this->imageGeoZone->getSudOuestPoint();
        $this->soTile = self::$gpm->LatLonToTile($so->getLatitude(), $so->getLongitude(), $this->zoom);
        $ne = $this->imageGeoZone->getNordEstPoint();
        $this->neTile = self::$gpm->LatLonToTile($ne->getLatitude(), $ne->getLongitude(), $this->zoom);
        
        $this->soTileInfo = self::$gpm->TileLatLonBounds($this->soTile['x'], $this->soTile['y'], $this->zoom);
        $this->soTileInfo['min']['lat'] = $this->soTileInfo['min']['lat']*-1;
        $this->soTileInfo['max']['lat'] = $this->soTileInfo['max']['lat']*-1;
        $this->neTileInfo = self::$gpm->TileLatLonBounds($this->neTile['x'], $this->neTile['y'], $this->zoom);
        $this->neTileInfo['min']['lat'] = $this->neTileInfo['min']['lat']*-1;
        $this->neTileInfo['max']['lat'] = $this->neTileInfo['max']['lat']*-1;
        $this->tilesGeoZone = new GeoZone($this->soTileInfo['max']['lat'], $this->neTileInfo['min']['lat'], $this->soTileInfo['min']['lon'], $this->neTileInfo['max']['lon']);
    }
    
    
    /**
     * Calcule la taille de l'image de sortie avant découpe
     */
    private function calculateOutputSize(){
        //Calcul de la zone réelle en pixels à couvrir
        $this->nbTilesX = (max($this->neTile['x'], $this->soTile['x']) - min($this->neTile['x'], $this->soTile['x']) + 1);
        $this->nbTilesY = (max($this->neTile['y'], $this->soTile['y']) - min($this->neTile['y'], $this->soTile['y']) + 1);
        $this->outputWidth = $this->nbTilesX * $this->tileZize;
        $this->outputHeight = $this->nbTilesY * $this->tileZize;
    }
    
    
    /**
     * Retourne la position X de la tuile SO
     * @return integer
     */
    public function getMinTileX(){
        return $this->soTile['x'];
    }
    
    
    /**
     * Retourne la position X de la tuile NE
     * @return integer
     */
    public function getMaxTileX(){
        return $this->neTile['x'];
    }
    
    
    /**
     * Retourne la position Y de la tuile NE
     * @return integer
     */
    public function getMinTileY(){
        return $this->neTile['y'];
    }
    
    
    /**
     * Retourne la position Y de la tuile SO
     * @return integer
     */
    public function getMaxTileY(){
        return $this->soTile['y'];
    }
    
    
    /**
     * Effectue la sortie de l'ensemble des tuiles
     * @param string $outputFolder                  Chemin du dossier de sortie des tuiles
     * @param string $fileNameTemplate              Template des noms de tuile générées. (Par défaut %zoom%_%tilex%_%tiley%.%ext%)
     * @param boolean $createFolderIfNotExists      Crée le dossier si il n'existe pas (TRUE par défaut)
     * @param boolean $deleteFolderContentBefore    Supprime le contenu du dossier avant génération des données (FALSE par défaut)
     * @param integer $opacity                      Opacité des tuiles - de 1 à 100. (100 par défaut)
     */
    public function build($outputFolder, $fileNameTemplate = '%zoom%_%tilex%_%tiley%.%ext%', $createFolderIfNotExists = true, $deleteFolderContentBefore = false, $opacity = 100){
        //Gestion du dossier de sortie
        $f = new Folder($outputFolder, '', $createFolderIfNotExists);
        if($deleteFolderContentBefore){
            $f->deleteContent();
        }
        

        //Calcul taille image à inserer
        $aso = $this->tilesGeoZone->getSudOuestPoint();
        $ane = new GeoPoint(max($this->soTileInfo['min']['lat'], $this->soTileInfo['max']['lat']), max($this->soTileInfo['min']['lon'], $this->soTileInfo['max']['lon']));
        $a = $this->imageGeoZone->getSudOuestPoint();
        
        $leftPixelMargin = NumberTools::floor($this->tileZize * (($a->getLongitude() - $aso->getLongitude()) / ($ane->getLongitude() - $aso->getLongitude())));
        $bottomPixelMargin = NumberTools::floor($this->tileZize * (($a->getLatitude() - $aso->getLatitude()) / ($ane->getLatitude() - $aso->getLatitude())));
        
        $bso = new GeoPoint(min($this->neTileInfo['min']['lat'], $this->neTileInfo['max']['lat']), min($this->neTileInfo['min']['lon'], $this->neTileInfo['max']['lon']));
        $bne = $this->tilesGeoZone->getNordEstPoint();
        $b = $this->imageGeoZone->getNordEstPoint();
        
        $rightPixelMargin = $this->tileZize - NumberTools::floor($this->tileZize * (($b->getLongitude() - $bso->getLongitude()) / ($bne->getLongitude() - $bso->getLongitude())));
        $topPixelMargin = $this->tileZize - NumberTools::floor($this->tileZize * (($b->getLatitude() - $bso->getLatitude()) / ($bne->getLatitude() - $bso->getLatitude())));
        
        $xTargetSize = (($this->nbTilesX - 2) * $this->tileZize) + ($this->tileZize - $leftPixelMargin) + ($this->tileZize - $rightPixelMargin);
        $yTargetSize = (($this->nbTilesY - 2) * $this->tileZize) + ($this->tileZize - $topPixelMargin) + ($this->tileZize - $bottomPixelMargin);
        
        //Preparation d el'image source aux bonnes dimensions
        $sourceImage = new Image($this->imagePath);
        $resizeFilter = new AbsoluteResize($xTargetSize, $yTargetSize);
        $sourceImage->addFilter($resizeFilter);
       
        //Generation de l'image full definition
        $full = new Image(null, $this->outputWidth, $this->outputHeight);
        $imageImport = new ImageImport(null, $sourceImage, $leftPixelMargin, $topPixelMargin);
        $full->addFilter($imageImport);
        if($opacity < 100){
            $imageOpacity = new Opacity($opacity);
            $full->addFilter($imageOpacity);
        }
        
        //Si masquage, on crée les rectangles nécessaires
        if($this->valideGeoZone){
            $y_c1 = $this->valideGeoZone->getNordOuestPoint();
            $y_c2 = $this->valideGeoZone->getSudOuestPoint();
            $y_b = $this->tilesGeoZone->getNordOuestPoint();
            $y_a = $this->tilesGeoZone->getSudOuestPoint();
            $topMargin = round($this->outputHeight * (($y_b->getLatitude() - $y_c1->getLatitude()) / ($y_b->getLatitude() - $y_a->getLatitude())));
            $bottomMargin = round($this->outputHeight - ($this->outputHeight * (($y_b->getLatitude() - $y_c2->getLatitude()) / ($y_b->getLatitude() - $y_a->getLatitude()))));
            
            $x_c1 = $this->valideGeoZone->getNordOuestPoint();
            $x_c2 = $this->valideGeoZone->getNordEstPoint();
            $x_a = $this->tilesGeoZone->getNordOuestPoint();
            $x_b = $this->tilesGeoZone->getNordEstPoint();
            $leftMargin = round($this->outputWidth * (($x_c1->getLongitude() - $x_a->getLongitude()) / ($x_b->getLongitude() - $x_a->getLongitude())));
            $rightMargin = round($this->outputWidth - ($this->outputWidth * (($x_c2->getLongitude() - $x_a->getLongitude()) / ($x_b->getLongitude() - $x_a->getLongitude()))));
            
            //Masque top
            $rectangleTop = new RectangleFill(0, 0, $this->outputWidth, $topMargin, $this->masquageColorR, $this->masquageColorG, $this->masquageColorB);
            $full->addFilter($rectangleTop);
            
            //Masque bottom
            $rectangleBottom = new RectangleFill(0, ($this->outputHeight - $bottomMargin), $this->outputWidth, $this->outputHeight, $this->masquageColorR, $this->masquageColorG, $this->masquageColorB);
            $full->addFilter($rectangleBottom);
            
            //Masque left
            $rectangleLeft = new RectangleFill(0, 0, $leftMargin, $this->outputHeight, $this->masquageColorR, $this->masquageColorG, $this->masquageColorB);
            $full->addFilter($rectangleLeft);
            
            //Masque right
            $rectangleRight = new RectangleFill(($this->outputWidth - $rightMargin), 0, $this->outputWidth, $this->outputHeight, $this->masquageColorR, $this->masquageColorG, $this->masquageColorB);
            $full->addFilter($rectangleRight);

        }
        
        
        $startTileY = $this->soTile['y'] - $this->nbTilesY + 1;
        $tileX = $this->soTile['x'];
        
        for($x = 0; $x < $this->nbTilesX; $x++){
            $tileY = $startTileY;
            
            for($y = 0; $y < $this->nbTilesY; $y++){
                $ip = $full->getImagePart($x * $this->tileZize, $y * $this->tileZize, $this->tileZize, $this->tileZize);
                $fileName = $fileNameTemplate;
                $fileName = StringTools::replaceAll($fileName, '%zoom%', $this->zoom);
                $fileName = StringTools::replaceAll($fileName, '%tilex%', $tileX);
                $fileName = StringTools::replaceAll($fileName, '%tiley%', $tileY);
                $fileName = StringTools::replaceAll($fileName, '%ext%', $full->getExtension());
                
                $ip->write($outputFolder.$fileName);
                $tileY++;
            }
            
            $tileX++;
        }
    }

}
