<?php
require_once 'db.php';

class UserManager {
	private DB $db;
	private PDO $pdo;

	function __construct(){
		$this->db = new DB();
		$this->pdo = $this->db->getDB();
	}

	# =========
	# == API ==
	# =========

	/**
	 * Register a player in the database.
	 *
	 * @param string $pseudo
	 * @param string $passwd
	 * @return boolean true if the operation is sucessfull
	 *         false if the player already exist
	 */
	public function registerPlayer(string $pseudo, string $passwd): bool{
		$passwdhash = password_hash($passwd, PASSWORD_ARGON2ID);
		return $this->registerPlayerDB($pseudo, $passwdhash);
	}

	/**
	 * Verify the the password provided by the player is correct.
	 *
	 * @param string $pseudo
	 * @param string $passwd
	 * @return boolean true if the password is verified
	 *         false if the password does not match or the player does not exist.
	 */
	public function verifyPassword(string $pseudo, string $passwd): bool{
		$info = $this->getInfosDB($pseudo);
		if(!$info || empty($info))
			return false;
		return password_verify($passwd, $info[0]["passwdhash"]);
	}

	/**
	 * Get the hightscore of the player.
	 *
	 * @param string $pseudo
	 * @return boolean|int false if the player does not exist,
	 *         the highscore otherwith.
	 */
	public function getHighscore(string $pseudo){
		$info = $this->getInfosDB($pseudo);
		if(!$info || empty($info))
			return false;
		return $info[0]["highscore"];
	}

	/**
	 * Set the password of the player.
	 *
	 * @param string $pseudo
	 * @param string $passwd
	 * @return bool true if sucessfull,
	 *         false if the player does not exist.
	 */
	public function setPassword(string $pseudo, string $passwd): bool{
		$passwdhash = password_hash($passwd, PASSWORD_ARGON2ID);
		$info = $this->getInfosDB($pseudo);
		if(!$info || empty($info))
			return false;
		return $this->setInfosDB($pseudo, $passwdhash, $info[0]["highscore"]);
	}

	/**
	 * Set the hightscore of the player.
	 *
	 * @param string $pseudo
	 * @param string $highscore
	 * @return bool true if sucessfull,
	 *         false if the player does not exist.
	 */
	public function setHighscore(string $pseudo, string $highscore): bool{
		if($highscore < 0)
			return false;
		$info = $this->getInfosDB($pseudo);
		if(!$info || empty($info))
			return false;
		return $this->setInfosDB($pseudo, $info[0]["passwdhash"], $highscore);
	}

	/**
	 * Delete a player from the database.
	 *
	 * @param string $pseudo
	 * @return bool true is the player have been sucessfully deleted,
	 *         false if it already does not exist.
	 */
	public function deletePlayer(string $pseudo): bool{
		return $this->deletePlayerDB($pseudo);
	}

	/**
	 * Get the list of players and hight score sort by hight score.
	 * 
	 * @return unknown
	 */
	public function getAllUserHightscore(){
		return $this->getAllUserHightscoreDB();
	}

	# ================
	# == DB QUERRY ==
	# ===============

	/**
	 * Register a player in the database.
	 *
	 * @param string $pseudo
	 * @param string $passwdhash
	 * @return bool
	 */
	private function registerPlayerDB(string $pseudo, string $passwdhash): bool{
		$stmt = $this->pdo->prepare("INSERT INTO 'User' (pseudo, passwdhash)
		VALUES (:pseudo, :passwdhash);");
		$stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		$stmt->bindValue(':passwdhash', $passwdhash, PDO::PARAM_STR);
		try{
			return $stmt->execute();
		}catch(PDOException $e){
			return false;
		}
	}

	/**
	 * Get Information about a player.
	 *
	 * @param string $pseudo
	 * @return unknown|boolean (psswdhash, hightscore)
	 */
	private function getInfosDB(string $pseudo){
		$stmt = $this->pdo->prepare("SELECT u.passwdhash, u.highscore
		FROM 'User' u
		WHERE u.pseudo = :pseudo;");
		$stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		try{
			$stmt->execute();
			return $this->db->fetchToMap($stmt);
		}catch(PDOException $e){
			return false;
		}
	}

	/**
	 * Set the informations concerning the player with the pseudo passed in argument.
	 *
	 * @param string $pseudo
	 * @param string $passwdhash
	 * @param int $highscore
	 * @return bool
	 */
	private function setInfosDB(string $pseudo, string $passwdhash, int $highscore): bool{
		$stmt = $this->pdo->prepare("UPDATE 'User'
		SET passwdhash = :passwdhash, highscore = :highscore
		WHERE pseudo = :pseudo;");
		$stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		$stmt->bindValue(':passwdhash', $passwdhash, PDO::PARAM_STR);
		$stmt->bindValue(':highscore', $highscore, PDO::PARAM_INT);
		try{
			return $stmt->execute();
		}catch(PDOException $e){
			return false;
		}
	}

	/**
	 * Delete a player from the database.
	 *
	 * @param string $pseudo
	 * @return bool
	 */
	private function deletePlayerDB(string $pseudo): bool{
		$stmt = $this->pdo->prepare("DELETE FROM 'User'
		WHERE pseudo = :pseudo;");
		$stmt->bindValue(':pseudo', $pseudo, PDO::PARAM_STR);
		try{
			$stmt->execute();
			return $stmt->rowCount() == 1;
		}catch(PDOException $e){
			return false;
		}
	}

	/**
	 * Get the list of players and hight score sort by hight score.
	 *
	 * @return unknown[]|boolean
	 */
	private function getAllUserHightscoreDB(){
		$stmt = $this->pdo->prepare("SELECT u.pseudo, u.highscore
		FROM 'User' u
		ORDER BY u.highscore DESC;");
		try{
			$stmt->execute();
			return $this->db->fetchToMap($stmt);
		}catch(PDOException $e){
			return false;
		}
	}
}
?>
