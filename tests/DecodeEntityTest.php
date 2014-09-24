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

	/**
	 * @dataProvider deserializeBlobProvider
	 */
	public function testDeserializeBlob( $file ) {
		$json = file_get_contents( $file );
		$codec = $this->getEntityContentDataCodec();

		try {
			$decodedItem = $codec->decodeEntity( $json, CONTENT_FORMAT_JSON );
		} catch ( \Exception $ex ) {
			echo "deserializtion failed for $file\n";
			throw $ex;
		}

		$this->assertTrue( true );
	}

	public function deserializeBlobProvider() {
		$cases = array();

		$files = $files = glob( __DIR__ . '/data/*.json' );

		foreach( $files as $file ) {
			$cases[] = array( $file );
		}

		return $cases;
	}

	private function getEntityContentDataCodec() {
		if ( !isset( $this->codec ) ) {
			$this->codec = WikibaseRepo::getDefaultInstance()->getEntityContentDataCodec();
		}

		return $this->codec;
	}

}
