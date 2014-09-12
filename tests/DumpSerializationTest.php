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

	/**
	 * @dataProvider deserializeBlobProvider
	 */
	public function testDeserializeBlob( $itemId, $message ) {
		$json = $this->fetchEntityBlob( $itemId );
		$codec = $this->getEntityContentDataCodec();

		try {
			$decodedItem = $codec->decodeEntity( $json, CONTENT_FORMAT_JSON );
		} catch ( \Exception $ex ) {
			echo "$itemId failed\n";
			throw $ex;
		}

		$this->assertTrue( true );
	}

	public function deserializeBlobProvider() {
		$cases = array();

		$pgConn = $this->getDbConn();
		$sql = "SELECT id FROM items ORDER BY id LIMIT 10000";
		$statement = $pgConn->query( $sql );

		while ( $row = $statement->fetch() ) {
			$id = $row['id'];
			$cases[] = array( $id, "Item Q$id" );
		}

		return $cases;
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
