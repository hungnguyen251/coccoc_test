<?php
require_once dirname(__DIR__, 1) . '/db/DB.php';

class Advert extends DB
{
    public function __construct()
    {
        parent::__construct();
        $this->setTableName('adverts');
    }

    function getAll($page = 1)
    {
        /**
         * 
         * @var object $this
        */
        try {
            $data = [];
            $sort = isset($_REQUEST['sort']) ? strval($_REQUEST['sort']) : '';
            $andSort = isset($_REQUEST['and']) ? strval($_REQUEST['and']) : '';
            $page = isset($_REQUEST['page']) ? strval($_REQUEST['page']) : 1;
            $offset = ($page - 1) * 10;
        
            if ('' != $sort) {
                $handleSort = explode(',', $sort);
        
                if (2 == count($handleSort)) {
                    $keySort = $handleSort['0'];
                    $valueSort = strtoupper($handleSort['1']);
        
                    $sort = " ORDER BY $keySort $valueSort";
        
                    if ('' != $andSort) {
                        $handleAndSort = explode(',', $andSort);
                        if (2 == count($handleAndSort)) {
        
                            $keyAndSort = $handleAndSort['0'];
                            $valueAndSort = strtoupper($handleAndSort['1']);
        
                            $sort = $sort . ", $keyAndSort $valueAndSort";
                        }
                    }
                }
            }
        
            $data = $this->db->query("SELECT ads.title, ads.price, img.link FROM adverts AS ads
                                    LEFT JOIN images AS img ON ads.id = img.advert_id GROUP BY img.advert_id
                                    $sort LIMIT $offset, 10
                                    ")->fetchAll(PDO::FETCH_ASSOC);
        
            echo json_encode([
                "code" => 200,
                "data" => $data
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                "code" => 400,
                "message" => $e->getMessage()
            ]);
        }
    }


    function getById($id)
    {
        /**
         * 
         * @var object $this
        */
        try {
            $data = [];
            $advertId = intval($id['id']);
        
            $data = $this->db->query("SELECT ads.title, ads.price, img.link, img.description FROM adverts AS ads
                                    LEFT JOIN images AS img ON ads.id = img.advert_id
                                    WHERE ads.id = $advertId
                                    ")->fetchAll(PDO::FETCH_ASSOC);
        
            echo json_encode([
                "code" => 200,
                "data" => $data
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                "code" => 400,
                "message" => $e->getMessage()
            ]);
        }
    }

    function create()
    {
        /**
         * 
         * @var object $this
        */
        try {
            $obj = file_get_contents("php://input");
            $data = json_decode($obj,true);
            
            //validate data
            $errors = $this->validateData($data);

            if (count($errors) > 0) {
                echo json_encode([
                    "code" => 400,
                    "data" => $errors
                ]);
                exit;
                
            } else {
                $this->db->beginTransaction();
                
                if (!empty($data['title']) && !empty($data['price'])) {
                    $price = doubleval($data['price']);
                    $title = strval($data['title']);
                    $advert = $this->db->prepare("INSERT INTO $this->tableName(title,price) VALUES (:title,:price)");
                    $advert->bindParam(":title", $title);
                    $advert->bindParam(":price", $price);

                    $advert->execute();
                }
                
                //last insert id
                $advertId = $this->db->lastInsertId();
                
                if (null !== $advertId && !empty($data['link'])) {
                    $values = '';

                    if (1 == count($data['link'])) {
                        $data['description'][0] = isset($data['description'][0]) ? $data['description'][0] : '';
    
                        $values .= "('".$advertId."', '".$data['link'][0]."', '".$data['description'][0]."')";

                    } else {
                        for ($i = 0; $i < count($data['link']); $i++) {
                            $separator = $i > 0 ? ',' : '';

                            $data['description'][$i] = isset($data['description'][$i]) ? $data['description'][$i] : '';
                            $values .= $separator . "('".$advertId."', '".$data['link'][$i]."', '".$data['description'][$i]."')";
                        }
                    }
                    
                    $image = $this->db->prepare("INSERT INTO images(advert_id,link,description) VALUES $values");
                    $image->execute();
                }
                $this->db->rollBack();
        
                echo json_encode([
                    "code" => 200,
                    "data" => intval($advertId)
                ]);
            }
        
        } catch (Exception $e) {
            echo json_encode([
                "code" => 400,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function getCount()
    {
        /**
         * 
         * @var object $this
        */
        $rs = $this->db->query("SELECT COUNT(*) as total FROM $this->tableName");
        return $rs->fetchObject();
    }

    function validateData($data) {
        $error = [];
        
        if (!isset($data['title'])){
            $error['title'] = 'title is require';
        } else {
            if (200 < strlen($data['title'])) {
                $error['title'] = 'title must be less than 200 characters';
            }
        }
     
        if (!isset($data['link'])){
            $error['link'] = 'link is require';
        } else {
            if (is_array($data['link']) && count($data['link']) > 3) {
                $error['link'] = 'advert has no more than 3 pictures';
            }
        }
    
        if (!isset($data['price'])){
            $error['price'] = 'price is require';
        } else {
            if (!is_numeric($data['price'])) {
                $error['price'] = 'price must be numeric';
            }
        }
    
        if (isset($data['description'])){
            if (is_array($data['description']) && count($data['description']) > 0) {
                for ($i = 0; $i < count($data['description']); $i++) {
                    if (1000 < strlen($data['description'][$i])) {
                        $error['description'] = 'description must be less than 1000 characters';
                        break;
                    }
                }
            }
        }
    
        return $error;
    }
}