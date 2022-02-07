<?php 
// http://phpqrcode.sourceforge.net/examples/index.php?example=001

include('phpqrcode/qrlib.php');

class Quick_response_code 
{
	/**
	 * Defines error correction level
	 */
	const QRC_ECLEVEL_L = 'L';
	const QRC_ECLEVEL_M = 'M';
	const QRC_ECLEVEL_Q = 'Q';
	const QRC_ECLEVEL_H = 'H';

	/**
	 * Defines upload path
	 */
	const UPLOAD_PATH = APPPATH . '..' . DIRECTORY_SEPARATOR . 'upload' . DIRECTORY_SEPARATOR . 'qrc' . DIRECTORY_SEPARATOR;

	/**
	 * Constructor
	 */
	public function __construct() 
	{
		if (! file_exists(self::UPLOAD_PATH)) {
			mkdir(self::UPLOAD_PATH);
		}
	}

	/**
	 * Creates QR code
	 *
	 * @param string $value The value for the QR code
	 * @param string $filename The name of the QR code image
	 * @param string $errorCorrectionLevel The error level
	 * @param integer $matrixPointSize The size of matrix point
	 * @param integer $margin The size of margin
	 * @param bool $saveandprint Prints image if true
	 */
	public function create(
		$value, 
		$filename, 
		$errorCorrectionLevel, 
		$matrixPointSize = 4, 
		$margin = 2, 
		$saveandprint = false,
		$back_color = 0xFFFFFF, 
		$fore_color = 0x000000
	) {
		$error_level = ['L','M','Q','H'];
		if (! in_array($errorCorrectionLevel, $error_level)) {
			throw new \Exception("Invalid error correction level! Expected 'L', 'M', 'Q', or 'H' but {$errorCorrectionLevel} provided in " . __METHOD__);
		}

		if ('.png' !== substr($filename, -4)) {
			throw new \Exception("Invalid extension! Expected '.png' but {$filename} provided in " . __METHOD__);
		}

		$file_path = self::UPLOAD_PATH . $filename;

		try {
			return QRcode::png(
				$value, 
				$file_path, 
				$errorCorrectionLevel, 
				$matrixPointSize,
				$margin,
				$saveandprint,
				$back_color, 
				$fore_color
			);
		} catch (\Exception $e) {
			// logs error message
			log_message('error', 'Error while creating QR code. ' . $e->getMessage());
		}
	}
}
