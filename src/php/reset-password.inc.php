<?php

if (isset($_POST['reset-password-submit'])) {
	$selector = $_POST['selector'];
	$validator = $_POST['validator'];
	$password = $_POST['password'];
	$confirmPassword = $_POST['confirmPassword'];

	include_once 'dbh.inc.php';

	class ResetPassword extends Dbh
	{
		protected function updatePassword($selector, $validator, $password)
		{
			$currentDate = date("U");
			$sql = "SELECT * FROM password_reset WHERE `reset_selector` = ? AND `reset_expires` >= ?;";
			$stmt = $this->connect()->prepare($sql);

			if (!$stmt->execute([$selector, $currentDate])) {
				$stmt = null;
				header("Location: ../reset-password.php?error=stmtfailed");
				exit();
			}
			if ($stmt->rowCount() == 0) {
				$stmt = null;
				header("Location: ../reset-password.php?error=invalidtoken");
				exit();
			}
			$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

			$tokenBin = hex2bin($validator);
			$tokenCheck = password_verify($tokenBin, $result[0]['reset_token']);

			if ($tokenCheck === false) {
				$stmt = null;
				header("Location: ../reset-password.php?error=invalidtoken");
				exit();
			} else if ($tokenCheck === true) {
				$tokenEmail = $result[0]['reset_email'];
				$sql = "SELECT * FROM users WHERE `email` = ?;";
				$stmt = $this->connect()->prepare($sql);

				if (!$stmt->execute([$tokenEmail])) {
					$stmt = null;
					header("Location: ../reset-password.php?error=stmtfailed");
					exit();
				}
				if ($stmt->rowCount() == 0) {
					$stmt = null;
					header("Location: ../reset-password.php?error=invalidtoken");
					exit();
				}
			}
			$sql = "UPDATE users SET `password` = ? WHERE `email` = ?;";
			$stmt = $this->connect()->prepare($sql);

			$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

			if (!$stmt->execute([$hashedPassword, $tokenEmail])) {
				$stmt = null;
				header("Location: ../reset-password.php?error=stmtfailed");
				exit();
			}
			$sql = "DELETE FROM password_reset WHERE `reset_email` = ?;";
			$stmt = $this->connect()->prepare($sql);

			if (!$stmt->execute([$tokenEmail])) {
				$stmt = null;
				header("Location: ../reset-password.php?error=stmtfailed");
				exit();
			}
			$stmt = null;
		}
	}

	class ResetPasswordContr extends ResetPassword
	{
		private $selector;
		private $validator;
		private $password;
		private $confirmPassword;

		public function __construct($selector, $validator, $password, $confirmPassword)
		{
			$this->selector = $selector;
			$this->validator = $validator;
			$this->password = $password;
			$this->confirmPassword = $confirmPassword;
		}
		public function resetPassword()
		{
			if ($this->emptyFields() == false) {
				header("Location: $_SERVER[HTTP_REFERER]&error=emptyfields");
				exit();
			}
			if ($this->validatePassword() == false) {
				header("Location: $_SERVER[HTTP_REFERER]&error=invalidpassword");
				exit();
			}
			if ($this->passwordsMatch() == false) {
				header("Location: $_SERVER[HTTP_REFERER]&error=passwordcheck");
				exit();
			}
			$this->updatePassword($this->selector, $this->validator, $this->password);
		}
		private function emptyFields()
		{
			if (empty($this->password) || empty($this->confirmPassword)) {
				$result = false;
			} else {
				$result = true;
			}
			return $result;
		}
		private function validatePassword()
		{
			if (strlen($this->password) < 8) {
				$result = false;
			} else {
				$result = true;
			}
			return $result;
		}
		private function passwordsMatch()
		{
			if ($this->password != $this->confirmPassword) {
				$result = false;
			} else {
				$result = true;
			}
			return $result;
		}
	}
	$resetPassword = new ResetPasswordContr($selector, $validator, $password, $confirmPassword);
	$resetPassword->resetPassword();

	header("Location: ../login.php?reset=passwordupdated");
} else {
	header("Location: ../index.php");
}
