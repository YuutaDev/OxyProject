<div class="loader">
	<img src="assets/logo/logo-footer.svg" alt="">
</div>
<header class="header__main">
	<a class="logo" href="home" title="Homepage">
		<img src="assets/logo/logo.svg" alt="Oxy Project Logo" />
		<div class="logo__text">OxyProject</div>
	</a>
	<div class="line"></div>
	<div class="left__side-mobile">
		<?php
		include "php/dbh.inc.php";
		if (isset($_SESSION['userId'])) {
			$userId = $_SESSION['userId'];
			include "php/UserContr.inc.php";
			$user = new UserContr($userId);
		?>
			<a class="btn btn-profile-mobile" href="dashboard">
				<img class="profile__icon-mobile" src="assets/icons/circle-user-regular.svg" alt="Profile" />
			</a>
		<?php
		} else {
		?>
			<a class="btn btn-profile-mobile" href="login">
				<img class="profile__icon-mobile" src="assets/icons/circle-user-regular.svg" alt="Profile" />
			</a>
		<?php
		}
		?>
		<div class="mobile__burger">
			<span class="mobile__burger_line"></span>
			<span class="mobile__burger_line"></span>
			<span class="mobile__burger_line"></span>
		</div>
	</div>
	<div class="mobile">
		<div class="mobile__menu">
			<form class="mobile__menu_search" action="" autocomplete="on">
				<div class="search__icon">
					<img src="assets/icons/search.svg" alt="Search" />
				</div>
				<input class="search__input" name="search" type="search" placeholder="Search arts, collections, and creators" />
			</form>
			<nav class="navbar">
				<div class="navbar__resources">
					<a class="nav__btn" href="collections"><img class="link__icon" src="assets/icons/explore.svg" alt="">Collections</a>
				</div>
				<div class="navbar__resources">
					<a class="nav__btn" href="contact"><img class="link__icon" src="assets/icons/contact.svg" alt="">Contact</a>
				</div>
				<div class="navbar__resources">
					<a class="nav__btn" href="about"><img class="link__icon" src="assets/icons/home.svg" alt="">About</a>
				</div>
			</nav>
			<?php
			if (isset($_SESSION['userId'])) {
			?>
				<a class="btn btn-profile" href="dashboard">
					<img class="profile__icon" src="assets/icons/circle-user-regular.svg" alt="Profile" title="Profile" />
				</a>
				<a class="btn btn-profile" href="php/logout.inc.php">
					<img class="profile__icon" src="assets/icons/logout.svg" alt="Logout" title="Logout" />
				</a>
			<?php
			} else {
			?>
				<a class="btn btn__gradient btn-connect" href="signup">Sign Up</a>
				<a class="btn  btn-profile" href="login">
					<img class="profile__icon" src="assets/icons/circle-user-regular.svg" alt="Profile" title="Login" />
				</a>
			<?php
			}
			?>
		</div>
		<div class="mobile__footer">
			<?php
			if (isset($_SESSION['userId'])) {
			?>
				<a class="btn btn__blue" href="php/logout.inc.php">
					Logout
				</a>
			<?php
			} else {
			?>
				<a class="btn btn__blue" href="signup">
					Sign Up
				</a>
			<?php
			}
			?>
			<div class="mobile__footer_bg">
				@@include('_share-buttons.php')
			</div>
		</div>
	</div>

</header>