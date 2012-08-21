<?php
##################################################
##												##
## Author:       Andrey Brykin (Drunya)         ##
## Version:      0.7                            ##
## Project:      CMS                            ##
## package       CMS Fapos                      ##
## subpackege    Geting file size function      ##
## copyright     ©Andrey Brykin 2010-2011       ##
##################################################


##################################################
##												##
## any partial or not partial extension         ##
## CMS Fapos,without the consent of the         ##
## author, is illegal                           ##
##################################################
## Любое распространение                        ##
## CMS Fapos или ее частей,                     ##
## без согласия автора, является не законным    ##
##################################################

// Возвращает размер файла в Кб
function getFileSize( $file )
{
  return number_format( (filesize($file)/1024), 2, '.', '' );
}



function deleteAttach($module, $matId, $attachNum) {
    $Register = Register::getInstance();
   	$FpsDB = $Register['DB'];

	
	$className = $Register['ModManager']->getModelNameFromModule($module . 'Attaches');
	$Model = new $className;
	$where = array(
		'entity_id' => $matId,
		'attach_number' => $attachNum,
	);
	$attach = $Model->getCollection($where, array('limit' => 1));

    if (count($attach) && is_array($attach)) {
        $filePath = ROOT . '/sys/files/' . $module . '/' . $attach[0]->getFilename();
        if (file_exists($filePath)) {
            _unlink($filePath);
        }
		$attach[0]->delete();
    }
    return true;
}

/**
 * Download attached files
 *
 * @param string $module
 * @param int $entity_id
 */
function downloadAttaches($module, $entity_id) {
	$Register = Register::getInstance();
	$FpsDB = $Register['DB'];

	$attaches = true;
	if (empty($entity_id) || !is_numeric($entity_id)) return false;
	$img_extentions = array('.png','.jpg','.gif','.jpeg', '.PNG','.JPG','.GIF','.JPEG');
	$files_dir = R . 'sys/files/' . $module . '/';
	// delete collizions if exists 
	//$this->deleteCollizions(array('id' => $post_id), true);
	
	
	$max_attach = $Register['Config']->read('max_attaches', $module);
	if (empty($max_attach) || !is_numeric($max_attach)) $max_attach = 5;
	for ($i = 1; $i <= $max_attach; $i++) {
		$attach_name = 'attach' . $i;
		if (!empty($_FILES[$attach_name]['name'])) {
		
		
			// Извлекаем из имени файла расширение
			$filename = getSecureFilename($_FILES[$attach_name]['name'], $files_dir);
			$ext = strrchr($_FILES[$attach_name]['name'], ".");

			
			$is_image = 0;
			if (($_FILES[$attach_name]['type'] == 'image/jpeg'
			|| $_FILES[$attach_name]['type'] == 'image/jpg'
			|| $_FILES[$attach_name]['type'] == 'image/gif'
			|| $_FILES[$attach_name]['type'] == 'image/png')
			&& in_array(strtolower($ext), $img_extentions)) {
				$is_image = 1;
			}

			// Перемещаем файл из временной директории сервера в директорию files
			if (move_uploaded_file($_FILES[$attach_name]['tmp_name'], $files_dir . $filename)) {
				chmod($files_dir . $filename, 0644);
				$attach_file_data = array(
					'entity_id'     => $entity_id,
					'user_id'       => $_SESSION['user']['id'],
					'attach_number' => $i,
					'filename'      => $filename,
					'size'          => $_FILES[$attach_name]['size'],
					'date'          => new Expr('NOW()'),
					'is_image'      => $is_image,
				);
				
				$className = ucfirst($module) . 'AttachesEntity';
				$entity = new $className($attach_file_data);
				$entity->save();
			}
		}
	}
	
	return $attaches;
}


/**
 * Create secure and allowed filename.
 * Check to dublicate;
 *
 * @param string $filename
 * @param string $dirToCheck - dirrectory to check by dublicate
 * @return string
 */
function getSecureFilename($filename, $dirToCheck) {
	if (empty($filename) || !is_string($filename)) {
		return md5(microtime().rand(0, 99999)) . '-' . date("Y-m-d-H-i-s");
	}
	
	
	$extentions = array('.php', '.phtml', '.php3', '.html', '.htm', '.pl', '.PHP', '.PHTML', '.PHP3', '.HTML', '.HTM', '.PL', '.js', '.JS');
	$ext = strrchr($filename, ".");
	$ext = (in_array( $ext, $extentions) || empty($ext)) ? '.txt' : $ext;
	$filename = mb_substr($filename, 0, mb_strlen($filename) - mb_strlen($ext));

	
	$filename = preg_replace('#[^a-z\d_\-]+#iu', 'x', $filename);
	while (file_exists($dirToCheck . $filename . $ext)) {
		$filename .= rand(0, 999);
		clearstatcache();
	}
	return $filename . $ext;
}
