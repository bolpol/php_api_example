<?php
#Главный класс с методами запросов к базе данных
class WalletController
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

	#Метод создания новой записи
	public function create(
	    $id_user,
        $base_address,
        $base_balance,
        $priv_key_base_address,
        $bonus_address,
        $bonus_balance,
        $priv_key_bonus_address
    )
	{
		try
		{
			$stmt = $this->db->prepare("INSERT INTO address_table(
                id_user, 
                priv_key_base_address,
                base_address,
                base_balance,
                bonus_address,
                bonus_balance, 
                priv_key_bonus_address) 
				VALUES(
				:id_user, 
				:priv_key_base_address, 
				:base_address, 
				:base_balance, 
				:bonus_address, 
				:bonus_balance, 
				:priv_key_bonus_address)"
            );
			$stmt->bindparam(":id_user",$id_user);
			$stmt->bindparam(":priv_key_base_address",$priv_key_base_address);
			$stmt->bindparam(":base_address",$base_address);
			$stmt->bindparam(":base_balance",$base_balance);
			$stmt->bindparam(":bonus_address",$bonus_address);
			$stmt->bindparam(":bonus_balance",$bonus_balance);
			$stmt->bindparam(":priv_key_bonus_address",$priv_key_bonus_address);
			$stmt->execute();

			return $this->api->response("Ok", "New pairs created", "true");
		}
		catch(PDOException $e)
		{
			return $this->api->response("Error", "Database error", "false");
		}
		
	}
	#получение по ID записи
	public function read($id)
	{
		$stmt = $this->db->prepare("SELECT * FROM address_table WHERE id=:id");
		$stmt->execute(array(":id"=>$id));
		$result=$stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

    public function update(
        $id,
        $priv_key_base_address,
        $base_address,
        $base_balance,
        $bonus_address,
        $bonus_balance,
        $priv_key_bonus_address
    )
    {
        try
        {
            $stmt=$this->db->prepare("UPDATE address_table 
                SET priv_key_base_address=:priv_key_base_address, 
		            base_address=:base_address, 
					bonus_address=:bonus_address, 
					priv_key_bonus_address=:priv_key_bonus_address
				WHERE id=:id ");
            $stmt->bindparam(":priv_key_base_address",$priv_key_base_address);
            $stmt->bindparam(":base_address",$base_address);
            $stmt->bindparam(":base_balance",$base_balance);
            $stmt->bindparam(":bonus_address",$bonus_address);
            $stmt->bindparam(":bonus_balance",$bonus_balance);
            $stmt->bindparam(":priv_key_bonus_address",$priv_key_bonus_address);
            $stmt->bindparam(":id",$id);
            $stmt->execute();

            return true;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

	#Удаление записи
	public function delete($id)
	{
	    try {
            $stmt = $this->db->prepare("DELETE FROM address_table WHERE id=:id");
            $stmt->bindparam(":id", (int) $id);
            $stmt->execute();
            return $this->api->response("Ok", "New pairs created", "true");
        }
        catch(PDOException $e)
        {
            return $this->api->response("Error", "Database error", "false");
        }
	}

	/*** ************ Service By USER ID (ETHEREUM ADDRESS) ************ */

    function getInfoByBtcAddress($base_address)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE base_address=:base_address");
        $stmt->execute(array(":base_address"=>$base_address));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Warning", "No current user", []);

        $response = [
            "id_user" => $result['id_user'],
            "base_balance" => $result['base_balance'],
            "bonus_address" => $result['bonus_address'],
            "bonus_balance" => $result['bonus_balance']
        ];

        return $this->api->response("Ok", "Users info", $response);;
    }

    function getInfoByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        $response = [
            "base_address" => $result['base_address'],
            "base_balance" => $result['base_balance'],
            "bonus_address" => $result['bonus_address'],
            "bonus_balance" => $result['bonus_balance']
        ];

        return $this->api->response("Ok", "Users info", $response);;
    }

    function getBaseAddress($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "base_address" => $result['base_address']
        ]);
    }

    function getBaseBalance($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "base_balance" => $result['base_balance']
        ]);
    }

    function getPrivateKeyBaseAddress($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "priv_key_base_address" => $result['priv_key_base_address']
        ]);
    }

    function getBonusAddress($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "bonus_address" => $result['bonus_address']
        ]);
    }

    function getBonusBalance($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "bonus_balance" => $result['bonus_balance']
        ]);
    }

    function getPrivateKeyBonusAddress($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->api->response("Ok", "Base address", [
            "priv_key_bonus_address" => $result['priv_key_bonus_address']
        ]);

    }

    /**
     * @param $id_user
     * @param $base_balance
     * @return bool
     */
    public function updateBaseBalance($id_user, $base_balance)
    {
        $response = "";
        try
        {
            $stmt=$this->db->prepare("UPDATE address_table SET base_balance=:base_balance WHERE id_user=:id_user");
            $stmt->bindparam(":base_balance", $base_balance);
            $stmt->bindparam(":id_user", $id_user);
            $stmt->execute();

            $response = $this->api->response("Ok", "Base btc address updated", []);
        }
        catch(PDOException $e)
        {
            $response = $this->api->response("Error", "database error", []);
        }
        return $response;
    }

    /**
     * @param $id_user
     * @param $bonus_balance
     * @return bool
     */
    public function updateBonusBalance($id_user, $bonus_balance)
    {
        $response = "";
        try
        {
            $stmt=$this->db->prepare("UPDATE address_table SET bonus_balance=:bonus_balance WHERE id_user=:id_user");
            $stmt->bindparam(":bonus_balance", $bonus_balance);
            $stmt->bindparam(":id_user", $id_user);
            $stmt->execute();

            $response = $this->api->response("Ok", "Bonus btc address updated", []);
        }
        catch(PDOException $e)
        {
            $response = $this->api->response("Error", "database error", []);
        }
        return $response;
    }

    public function deleteByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM address_table WHERE id_user=:id_user");
        $stmt->execute(array(":id_user"=>$user_id));
        $result=$stmt->fetch(PDO::FETCH_ASSOC);

        if(!$result) return $this->api->response("Error", "User not found", []);

        return $this->delete($result["id"]);
    }

}
