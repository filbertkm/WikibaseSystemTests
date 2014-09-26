<?php

namespace WikibaseSystemTests\Maintenance;

use Wikibase\DataModel\Entity\Item;
use Wikibase\Repo\WikibaseRepo;

$IP = getenv( 'MW_INSTALL_PATH' );
$basePath = $IP !== false ? $IP : __DIR__ . '/../../..';

require_once "$basePath/maintenance/Maintenance.php";

/**
 * @group Wikibase
 * @group WikibaseSerialization
 *
 * @licence GNU GPL v2+
 */
class DumpChecker extends \Maintenance {

	private static $dbConn;

	private $codec;

	public function execute() {
		$pgConn = $this->getDbConn();

		$min = 1000000;
		$max = 1001000;

		while ( $max <= 5000000 ) {
			$sql = "SELECT id FROM items WHERE id >= $min AND id < $max ORDER BY id";

			$min = $min + 1000;
			$max = $max + 1000;

			$statement = $pgConn->query( $sql );

			while ( $row = $statement->fetch() ) {
				$itemId = $row['id'];
				$json = $this->fetchEntityBlob( $row['id'] );
				$this->assertCanDeserialize( $json, $itemId );
			}

			$this->output( "Processed up to $itemId\n" );
		}

		$this->output( "Done: $itemId\n" );
	}

	private function assertCanDeserialize( $json, $itemId ) {
		$codec = $this->getEntityContentDataCodec();

		try {
			$decodedItem = $codec->decodeEntity( $json, CONTENT_FORMAT_JSON );
		} catch ( \Exception $ex ) {
			echo "$itemId failed\n";
			throw $ex;
		}
	}

	private function getDbConn() {
		if ( !isset( self::$dbConn ) ) {
			global $wgWBDumpDbConfig;

			self::$dbConn = \Doctrine\DBAL\DriverManager::getConnection(
				$wgWBDumpDbConfig,
				new \Doctrine\DBAL\Configuration()
			);
		}

		return self::$dbConn;
	}

	private function getEntityContentDataCodec() {
		if ( !isset( $this->codec ) ) {
			$this->codec = WikibaseRepo::getDefaultInstance()->getEntityContentDataCodec();
		}

		return $this->codec;
	}

	private function fetchEntityBlob( $numericId ) {
		$pgConn = $this->getDbConn();

		$sql = "SELECT * FROM items where id = $numericId";
		$statement = $pgConn->query( $sql );

		while ( $row = $statement->fetch() ) {
			return $row['content'];
		}

		throw new \Exception( 'entity not found' );
	}

}

$maintClass = 'WikibaseSystemTests\Maintenance\DumpChecker';
require_once RUN_MAINTENANCE_IF_MAIN;
