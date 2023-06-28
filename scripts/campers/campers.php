<?php

namespace APP\campers;

use APP\db\connect;
use APP\getInstance;

class campers extends connect
{
    private $queryPost = 'INSERT INTO campers (idCamper,nombreCamper,apellidoCamper,fechaNac,idReg) VALUES (:id_camper,:nombre_camper,:apellido_camper,:fecha_nacimiento,:id_region)';
    private $queryPut = 'UPDATE campers SET idCamper = :id_camper, nombreCamper = :nombre_camper, apellidoCamper = :apellido_camper, fechaNac = :fecha_nacimiento,idReg=:id_region   WHERE  idCamper = :id_camper';
    private $queryGetAll = 'SELECT  idCamper AS "id_camper", nombreCamper AS "nombre_camper",apellidoCamper AS "apellido_camper",fechaNac AS "fecha_nacimiento",idReg AS "id_region" FROM campers';
    private $queryDelete = 'DELETE FROM campers WHERE idCamper = :id_camper';
    private $message;

    use getInstance;
    //*Se definen el tipo de dato: static, private, public
    function __construct(private $id_camper = 1, public $nombre_camper = 1, public $apellido_camper = 1, public $fecha_nacimiento = 1, private $id_region = 1)
    {
        parent::__construct();

    }
    public function post_campers()
    {
        /*Prepare es literalmente preparar el query */
        $res = $this->conx->prepare($this->queryPost);
        /**Todas las solicitudes, así sea un connect deben intentarse dentro de un try-catch */
        try {
            /**El bindValue le asigna valores al alias que puse en el queryPost */
            $res->bindValue("id_camper", $this->id_camper);
            $res->bindValue("nombre_camper", $this->nombre_camper);
            $res->bindValue("apellido_camper", $this->apellido_camper);
            $res->bindValue("fecha_nacimiento", $this->fecha_nacimiento);
            $res->bindValue("id_region", $this->id_region);
            /**Execute es para ejecutar */
            $res->execute();
            $this->message = ["Code" => 200 + $res->rowCount(), "Message" => "Inserted data", "res" => $res];
        } catch (\PDOException $e) {
            /**Message es un array asociativo */
            $this->message = ["Code" => $e->getCode(), "Message" => $res->errorInfo()[2]];
        } finally {
            echo json_encode($this->message);
        }
    }

    public function update_campers($id)
    {
        /*Prepare es literalmente preparar el query */
        $res = $this->conx->prepare($this->queryPut);
        /**Todas las solicitudes, así sea un connect deben intentarse dentro de un try-catch */
        try {
            /**El bindValue le asigna valores al alias que puse en el queryPut */
            $res->bindParam("id_camper", $id);
            $res->bindValue("nombre_camper", $this->nombre_camper);
            $res->bindValue("apellido_camper", $this->apellido_camper);
            $res->bindValue("fecha_nacimiento", $this->fecha_nacimiento);
            $res->bindValue("id_region", $this->id_region);
            /**Execute es para ejecutar */
            $res->execute();
            if ($res->rowCount() > 0) {
                $this->message = ["Code" => 200 + $res->rowCount(), "Message" => "Data updated"];
            } else {
                $this->message = ["Code" => 404, "Message" => "Data not founded"];

            }
        } catch (\PDOException $e) {
            /**Message es un array asociativo */if ($e->getCode() == 23000) {
                $pattern = '/`([^`]*)`/';
                preg_match_all($pattern, $res->errorInfo()[2], $matches);
                $matches = array_values(array_unique($matches[count($matches) - 1]));
                $this->message = ["Code" => $e->getCode(), "Message" => "Error, no se puede actualizar ya que el id indicado de la llave foranea  no contiene registros asociados en la tabla padre"];
            } else {
                $this->message = ["Code" => $e->getCode(), "Message" => $res->errorInfo()[2]];
            }
        } finally {
            echo json_encode($this->message);

        }
    }

    public function delete_campers($id)
    {
        /*Prepare es literalmente preparar el query */
        $res = $this->conx->prepare($this->queryDelete);
        /**Todas las solicitudes, así sea un connect deben intentarse dentro de un try-catch */
        try {
            $res->bindParam("id_camper", $id);
            /**Execute es para ejecutar */
            $res->execute();

            if ($res->rowCount() > 0) {
                $this->message = ["Code" => 200 + $res->rowCount(), "Message" => "Data deleted"];
            } else {
                $this->message = ["Code" => 404, "Message" => "Data not founded"];
            }
        } catch (\PDOException $e) {
            /**Message es un array asociativo */
            if ($e->getCode() == 23000) {
                $pattern = '/`([^`]*)`/';
                preg_match_all($pattern, $res->errorInfo()[2], $matches);
                $matches = array_values(array_unique($matches[count($matches) - 1]));
                $this->message = ["Code" => $e->getCode(), "Message" => "Error, no se puede eliminar el id inicado ya que contiene registros asociados en la tabla $matches[1]"];
            } else {
                $this->message = ["Code" => $e->getCode(), "Message" => $res->errorInfo()[2]];
            }
        } finally {
            echo json_encode($this->message);
        }
    }

    public function getAll_campers()
    {
        try {
            $res = $this->conx->prepare($this->queryGetAll);
            $res->execute();
            $this->message = ["Code" => 200, "Message" => $res->fetchAll(\PDO::FETCH_ASSOC)];
        } catch (\PDOException $e) {
            $this->message = ["Code" => $e->getCode(), "Message" => $res->errorInfo()[2]];
        } finally {
            echo json_encode($this->message);
        }
    }
}
?>