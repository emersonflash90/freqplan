<?php
namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Site;
use Kairos\SpreadsheetReader\SpreadsheetReader;

/**
 * Description of ImportService
 *
 * @author Eric TONYE
 */
class ImportService {
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function importSiteFromFile($file_path) {
        // If you need to parse XLS files, include php-excel-reader
        $Reader = new SpreadsheetReader($file_path);
        $Sheets = $Reader->Sheets();
        $site = new Site();
        foreach ($Sheets as $Index => $Name) {
            if ($Index == 0) {
                $Reader->ChangeSheet($Index);
                $i = 0;
                foreach ($Reader as $Row) {
                    if ($i >0) {
                        if ($Row[0] == "") {
                            continue;
                        }
                        $site = $this->saveSite($Row);
                    }
                    $i++;
                }
            }
            break;
        }
        return $site;
    }
    
    public function saveSite($Row = array()){
        $site = new Site();
        $repositorySite = $this->em->getRepository("AppBundle:Site");
        $siteUnique = $repositorySite->findOneBy(array("tNumber" => $Row[0], "statut" => 1));
        if ($siteUnique == null && $Row[0] != "") {
            $site->setTNumber($Row[0]);
            $site->setSiteName($Row[1]);
            $site->setLatitude(doubleval($Row[2]));
            $site->setLongitude(doubleval($Row[3]));
            $site->setSiteCity($Row[4]);
            if($Row[5] == "E"){
                $site->setSiteType(1);
            }elseif($Row[5] == "N"){
                $site->setSiteType(2);
            }
            $site = $repositorySite->saveSite($site);
        }
        return $site;
    }
}
