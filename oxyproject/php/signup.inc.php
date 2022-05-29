<?php
 if (isset($_POST['submit'])) { $fullName = $_POST['fullName']; $username = $_POST['username']; $email = $_POST['email']; $password = $_POST['password']; $confirmPassword = $_POST['confirmPassword']; $checkbox = $_POST['checkbox']; include "dbh.inc.php"; class Signup extends Dbh { protected function setUser($fullName, $username, $email, $password) { $sql = "INSERT INTO users (`full_name`, `username`, `email`, `password`) VALUES (?, ?, ?, ?);"; $stmt = $this->connect()->prepare($sql); $hashedPassword = password_hash($password, PASSWORD_BCRYPT); if (!$stmt->execute([$fullName, $username, $email, $hashedPassword])) { $stmt = null; header("Location: ../signup.php?error=stmtfailed"); exit(); } $stmt = null; } protected function checkUser($username, $email) { $sql = "SELECT `id` FROM users WHERE `username` = ? OR `email` = ?;"; $stmt = $this->connect()->prepare($sql); if (!$stmt->execute([$username, $email])) { $stmt = null; header("Location: ../signup.php?error=stmtfailed"); exit(); } if ($stmt->rowCount() > 0) { $result = false; } else { $result = true; } return $result; } } class SignupContr extends Signup { private $fullName; private $username; private $email; private $password; private $confirmPassword; private $checkbox; public function __construct($fullName, $username, $email, $password, $confirmPassword, $checkbox) { $this->fullName = $fullName; $this->username = $username; $this->email = $email; $this->password = $password; $this->confirmPassword = $confirmPassword; $this->checkbox = $checkbox; } public function signupUser() { if ($this->emptyFields() == false) { header("Location: ../signup.php?error=emptyfields"); exit(); } if ($this->validateFullName() == false) { header("Location: ../signup.php?error=invalidfullname"); exit(); } if ($this->validateUsername() == false) { header("Location: ../signup.php?error=invalidusername"); exit(); } if ($this->validateEmail() == false) { header("Location: ../signup.php?error=invalidmail"); exit(); } if ($this->validatePassword() == false) { header("Location: ../signup.php?error=invalidpassword"); exit(); } if ($this->passwordsMatch() == false) { header("Location: ../signup.php?error=passwordcheck"); exit(); } if ($this->validateCheckbox() == false) { header("Location: ../signup.php?error=checkbox"); exit(); } if ($this->userTakenCheck() == false) { header("Location: ../signup.php?error=userexists"); exit(); } $this->setUser($this->fullName, $this->username, $this->email, $this->password); } private function emptyFields() { if (empty($this->fullName) || empty($this->username) || empty($this->email) || empty($this->password) || empty($this->confirmPassword)) { $result = false; } else { $result = true; } return $result; } private function validateFullName() { if (!preg_match("/^[a-zA-Z ]*$/", $this->fullName)) { $result = false; } else if (strlen($this->fullName) < 2 || strlen($this->fullName) > 25) { $result = false; } else { $result = true; } return $result; } private function validateUsername() { if (!preg_match("/^[a-zA-Z0-9]*$/", $this->username)) { $result = false; } else { $result = true; } return $result; } private function validateEmail() { if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) { $result = false; } else { $result = true; } return $result; } private function validatePassword() { if (strlen($this->password) < 8) { $result = false; } else { $result = true; } return $result; } private function passwordsMatch() { if ($this->password != $this->confirmPassword) { $result = false; } else { $result = true; } return $result; } private function validateCheckbox() { if (!filter_has_var(INPUT_POST, $this->checkbox)) { $result = false; } else { $result = true; } return $result; } private function userTakenCheck() { if (!$this->checkUser($this->username, $this->email)) { $result = false; } else { $result = true; } return $result; } } $signup = new SignupContr($fullName, $username, $email, $password, $confirmPassword, $checkbox); $signup->signupUser(); header("Location: ../login.php?signup=success"); } else { header("Location: ../index.php"); } 