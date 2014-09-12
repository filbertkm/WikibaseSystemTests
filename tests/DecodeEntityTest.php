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
class DecodeEntityTest extends \PHPUnit_Framework_TestCase {

	public function testDeserializeBlob() {
		$json = file_get_contents( __DIR__ . '/data/testwikidata-Q33.json' );
		$codec = $this->getEntityContentDataCodec();

		try {
			$decodedItem = $codec->decodeEntity( $json, CONTENT_FORMAT_JSON );
		} catch ( \Exception $ex ) {
			echo "deserializtion failed\n";
			throw $ex;
		}

		$this->assertTrue( true );
	}

	private function getEntityContentDataCodec() {
		if ( !isset( $this->codec ) ) {
			$this->codec = WikibaseRepo::getDefaultInstance()->getEntityContentDataCodec();
		}

		return $this->codec;
	}

}
