<?php

namespace Wikibase\Test;

use Wikibase\DataModel\Entity\Item;
use Wikibase\Repo\WikibaseRepo;

/**
 * @group Wikibase
 * @group WikibaseSerialization
 *
 * @licence GNU GPL v2+
 */
class DumpSerializationTest extends \PHPUnit_Framework_TestCase {

	private static $dbConn;

	private $codec;

	public function testDeserializeBlob() {
		$pgConn = $this->getDbConn();

		$min = 0;
		$max = 1000;

		while ( $max <= 100000 ) {
			$sql = "SELECT id FROM items WHERE id >= $min AND id < $max ORDER BY id";

			$min = $min + 1000;
			$max = $max + 1000;

			$statement = $pgConn->query( $sql );

			while ( $row = $statement->fetch() ) {
				$itemId = $row['id'];
				$json = $this->fetchEntityBlob( $row['id'] );
				$this->assertCanDeserialize( $json, $itemId );
			}
		}
	}

	private function assertCanDeserialize( $json, $itemId ) {
		$codec = $this->getEntityContentDataCodec();

		try {
			$decodedItem = $codec->decodeEntity( $json, CONTENT_FORMAT_JSON );
		} catch ( \Exception $ex ) {
			echo "$itemId failed\n";
			throw $ex;
		}

		$this->assertTrue( true );
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
