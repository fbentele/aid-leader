<?php

/**
 * ------------------------------------------------------------------------
 * ----
 * "THE BEER-WARE LICENSE" (Revision 42):
 * <macki@dracheburg.ch> wrote this file. As long as you retain this
 * notice you
 * can do whatever you want with this stuff. If we meet some day, and you
 * think
 * this stuff is worth it, you can buy me a beer in return. JoÃ«l Stampfli
 * / Macki
 * ------------------------------------------------------------------------
 * ----
 * getscoutDB.php v0.1
 * Gets person details of a group from db.scout.ch
 * scoutDB->getGroups("<groupID1>,<groupID2>...<groupIDN>")
 * Returns array:
 * $people
 * "id"
 * "nickname"
 * "email"
 * "first_name"
 * "last_name"
 * "address"
 * "zip_code"
 * "town"
 * "href"
 * "role1"
 * "role2"
 * ...
 * "phone1"
 * "phone2"
 * ...
 * macki@dracheburg.ch
 */
class Scout_DB {
	private $user;
	private $authToken;
	private $urlBase;
	private $arrLabels = array( "Spezialfunktion", "Fonction spéciale", "funzione speciale" );

	function __construct($urlBase) {
		$this->arrLabels = explode( ",", utf8_encode( implode( ",", $this->arrLabels ) ) );
		$this->urlBase = $urlBase;
	}

	function login( $user, $password ) {
		$this->user = $user;
		$url  = $this->urlBase . "/users/sign_in";
		$ch   = curl_init();
		$data = array( "person[email]" => $this->user, "person[password]" => $password );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json' ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $ch );
		curl_close( $ch );
		$decoded = json_decode( $result, true );

		if ( isset( $decoded['error'] ) ) {
			echo $result;
			die();
		} else {
			$this->authToken = $decoded["people"][0]["authentication_token"];
		}
	}

	function qry( $qry ) {
		$url       = $this->urlBase . "/groups" . $qry . ".json";
		$ch        = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'X-User-Email: ' . $this->user,
			'X-User-Token: ' . $this->authToken,
			'Accept: application/json'
		) );
		$result = curl_exec( $ch );
		curl_close( $ch );
		$decoded = json_decode( $result, true );

		return $decoded;
	}

	function getGroups($groups2get) {
		$data    = array();
		$people  = array();
		$groupId = explode( ",", $groups2get );
		foreach ( $groupId as $id ) {
			$qry  = "/" . $id . "/people";
			$data = $this->qry( $qry );
		}
		$numbers = array();
		$roles   = array();
		if ( isset( $data["linked"] ) ) {
			foreach ( $data["linked"]["phone_numbers"] as $number ) {
				$numbers[ $number["id"] ] = $number["number"];
			}
			foreach ( $data["linked"]["roles"] as $role ) {
				if ( in_array( $role["role_type"], $this->arrLabels ) ) {
					$roles[ $role["id"] ] = $role["label"];
				} else {
					$roles[ $role["id"] ] = $role["role_type"];
				}
			}
		}
		if ( isset( $data["people"] ) ) {
			for ( $i = 0; $i < count( $data["people"] ); $i ++ ) {
				$people[ $i ] = array(
					"id"         =>
						$data["people"][ $i ]["id"],
					"nickname"   =>
						$data["people"][ $i ]["nickname"],
					"email"      =>
						$data["people"][ $i ]["email"],
					"first_name" =>
						$data["people"][ $i ]["first_name"],
					"last_name"  =>
						$data["people"][ $i ]["last_name"],
					"address"    =>
						$data["people"][ $i ]["address"],
					"zip_code"   =>
						$data["people"][ $i ]["zip_code"],
					"town"       =>
						$data["people"][ $i ]["town"],
					"href"       =>
						$data["people"][ $i ]["href"],
				);
				$roleids      = $data["people"][ $i ]["links"]["roles"];
				$j            = 0;
				foreach ( $roleids as $id ) {
					if ( array_key_exists( $id, $roles ) ) {
						$roleindex                  = "role" . $j;
						$people[ $i ][ $roleindex ] = $roles[ $id ];
					}
					$j ++;
				}
				if ( array_key_exists( "phone_numbers",
					$data["people"][ $i ]["links"] ) ) {
					$numids =
						$data["people"][ $i ]["links"]["phone_numbers"];
					$j      = 0;
					foreach ( $numids as $id ) {
						if ( array_key_exists( $id, $numbers ) ) {
							$numindex                  = "phone" . $j;
							$people[ $i ][ $numindex ] = $numbers[ $id ];
						}
						$j ++;
					}
				}
			}
		}

		return $people;
	}
}