<?php

class UsersController
{
	#Переменная базы данных
	private $db;
    private $api;

	#Входящая точка подключения к базе данных
	function __construct($DB_con, API $api)
	{
		$this->db = $DB_con;
        $this->api = $api;
	}

    /**
     * @param $mode
     * @return string
     */
	private function read($mode)
	{
		$stmt = $this->db->prepare("SELECT * FROM users_table WHERE mode=:mode");
		$stmt->execute(array(":mode"=>$mode));
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		return $this->api->response("Ok", "Success", $result);
	}

    /**
     * @param $mode
     * @return string
     */
    public function generateUser($mode)
    {
        if($mode !== "write" || $mode !== "read") {
            return $this->api->response("Error", "Invalid mode", "");
        }

        $stmt = $this->db->prepare("INSERT INTO users_table(
                token, nonce, mode
                ) 
				VALUES(
				:token, :nonce, :mode)"
        );
        $stmt->bindparam(":token", hash("ripemd128", time() . rand(0, 999)));
        $stmt->bindparam(":nonce", 1);
        $stmt->bindparam(":mode", $mode);
        $stmt->execute();

        $stmt = $this->db->prepare("SELECT * FROM users_table WHERE mode=:mode");
        $stmt->execute(array(":mode"=>$mode));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);
        $response = [
            "key" => $result['token'],
            "mode" => $result['mode'],
            "nonce" => $result['nonce']
        ];
        return $this->api->response("Ok", "Success", $response);
    }

    /**
     * @param $hmac
     * @return string
     */
    public function updateKey($hmac)
    {

        $query = $this->db->prepare("SELECT * FROM users_table");
        $query->execute();
        $result = $query->fetchAll();

        foreach ($result as $item) {

            $localId    = $item["id"];
            $token      = $item["token"];
            $localNonce = $item["nonce"];
            // Sign the key with the nonce to get the tmp key
            $hmacKey = hash_hmac ( 'sha256', $token, $localNonce );
            // rebuild the string to sign
            $localHmac = hash_hmac ( 'sha256', $localId , $hmacKey );

            if(hash_equals($localHmac, $hmac)) {
                try
                {
                    $stmt=$this->db->prepare("UPDATE users_table 
                    SET token=:token, nonce=:nonce
                    WHERE id=:id");

                    $stmt->bindparam(":token", hash("ripemd128", time() . rand(0, 999)));
                    $stmt->bindparam(":nonce", 1);
                    $stmt->bindparam(":id", $localId);
                    $stmt->execute();

                    return $this->api->response("Ok", "Success updated", "");
                }
                catch(PDOException $e)
                {
                    return $this->api->response("Error", $e->getMessage(), "");
                }
            }
        }
    }

    /**
     * @param $token
     * @param $nonce
     * @return bool
     */
    public function incNonce($token, $nonce)
    {
        $nonce = (int) $nonce + 1;

        try
        {
            $stmt=$this->db->prepare("UPDATE users_table SET nonce=:nonce WHERE token=:token");
            $stmt->bindparam(":nonce", $nonce);
            $stmt->bindparam(":token", $token);
            $stmt->execute();

            return true;
        }
        catch(PDOException $e)
        {
            //echo $e->getMessage();
            return false;
        }
    }

    /**
     * @param $hmac
     * @param $nonce
     * @return bool
     */
    public function validateOnApi($hmac, $nonce)
    {
        $isValid = false;

        $query = $this->db->prepare("SELECT * FROM users_table");
        $query->execute();
        $result = $query->fetchAll();

        foreach ($result as $item) {

            $localId = $item["id"];
            $token = $item["token"];
            $localNonce = $item["nonce"];
            // Sign the key with the nonce to get the tmp key
            $hmacKey = hash_hmac ( 'sha256', $token, $localNonce );
            // rebuild the string to sign
            $localHmac = hash_hmac ( 'sha256', $localId , $hmacKey );

            if(hash_equals($localHmac, $hmac)) {
                $this->db->incNonce($token, $nonce);
                return $isValid = true;
            }
        }
        return $isValid;
    }

    /**
     * @param $hmac
     * @param $nonce
     * @return bool
     */
    public function validateOnAdmin($hmac, $nonce)
    {
        $isValid = false;

        $mode = "write";
        $query = $this->db->prepare("SELECT * FROM users_table WHERE mode=:mode");
        $query->bindValue(':mode', $mode);
        $query->execute();
        $result = $query->fetchAll();

        foreach ($result as $item) {

            $localId = $item["id"];
            $token = $item["token"];
            $localNonce = $item["nonce"];
            // Sign the key with the nonce to get the tmp key
            $hmacKey = hash_hmac ( 'sha256', $token, $localNonce );
            // rebuild the string to sign
            $localHmac = hash_hmac ( 'sha256', $localId , $hmacKey );

            if(hash_equals($localHmac, $hmac)) {
                $this->db->incNonce($token, $nonce);
                return $isValid = true;
            }
        }
        return $isValid;
    }
}
