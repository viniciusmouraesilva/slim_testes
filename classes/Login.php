<?php
class Login
{
	private $email;
	private $password;
	
	public function verificarLogin($dados = [])
	{
		$email = $dados['email'] ?? '';
		$senha = $dados['senha'] ?? '';
		
		if ($email === false or $senha === false) {
			return false;
		}
		
		$sql = "SELECT * FROM usuarios WHERE email = :email and senha = :senha LIMIT 1";
	
		$stmt = $this->db->prepare($sql);	
	
		$stmt->bindValue(':email', $dados['email'], PDO::PARAM_STR);
		$stmt->bindValue(':senha', $dados['senha'], PDO::PARAM_STR);
		
		$stmt->execute();
		
		if ($stmt->rowCount() === 1) {
			return true;
		} else {
			return false;		
		}
	}
}
