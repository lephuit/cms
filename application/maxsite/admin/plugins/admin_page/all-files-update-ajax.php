<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// проверим залогиненность
if (!is_login()) die('no login');

// проверим разрешение на редактирование записей
if (!mso_check_allow('admin_page_edit')) die('no allow');

if ( $post = mso_check_post(array('dir')) )
{
	mso_checkreferer(); // защищаем реферер
	
	$current_dir = $post['dir'];

	$all_files_res = '';

	$uploads_dir = getinfo('uploads_dir') . $current_dir;
	$uploads_url = getinfo('uploads_url') . $current_dir;
	
	
	$CI = & get_instance();
	$CI->load->helper('directory');
	$CI->load->helper('file');
	
	// все файлы в массиве $dirs
	$dirs = directory_map($uploads_dir, 2); // только в текущем каталоге

	if (!$dirs) $dirs = array();

	asort($dirs);
	
	
	$fn_mso_descritions = $uploads_dir . '/_mso_i/_mso_descriptions.dat';
	if (file_exists( $fn_mso_descritions )) 
	{
		// массив данных: fn => описание )
		// получим из файла все описания
		$mso_descritions = unserialize( read_file($fn_mso_descritions) );
	}
	else $mso_descritions = array();
	
	foreach ($dirs as $file)
	{
		if (is_array($file)) continue; // каталог — это массив — нам здесь не нужен
		
		$title = $title_f = '';
		
		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		
		$this_img = ($ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png');
		
		
		if (isset($mso_descritions[$file]))
		{
			$title = $mso_descritions[$file];
			// if ($title) $title_f = '<em>' . htmlspecialchars($title) . '</em><br>';
		}
		
		if ($this_img and file_exists($uploads_dir . '/mini/' . $file)) 
		{
			$mini = $uploads_url . '/mini/' . $file;
			
			$mini_100 = $uploads_url . '/_mso_i/' . $file;
			
			$mini = '<a class="lightbox" target="_blank" title="' . $title . '" href="' . $uploads_url. '/' . $file . '"><img src="' . $mini_100 . '"></a> ';
		}
		else 
		{
			$mini = '<img src="' . getinfo('admin_url') . 'plugins/admin_files/document_plain.png">';
		}
		
		if ($this_img)
		{
			if ($title)
			{
				$img = '\n[img ' . $title . ']' . $uploads_url . '/' . $file . '[/img]\n';
				
				$image = '\n[image=' . $uploads_url . '/mini/' . $file . ' ' . $title . ']' . $uploads_url . '/' . $file . '[/image]\n';
			}
			else
			{
				$img = '\n[img]' . $uploads_url . '/' . $file . '[/img]\n';
				
				$image = '\n[image=' . $uploads_url . '/mini/' . $file . ']' . $uploads_url . '/' . $file . '[/image]\n';
			}
		}
		
		$all_files_res .= '<div class="all-files-image">' 
					. $mini 
					. '<span title="' . t('Адрес файла') . '" onclick="jAlert(\'<textarea cols=70 rows=3>' . $uploads_url . '/' . $file . '</textarea>\', \'' . t('Адрес файла') . '\'); return false;">Адрес</span>';
					
		if ($this_img)			
		{
			$all_files_res .= '
					<span title="' . t('Изображение') . '" onclick="addSmile(\'' . $img . '\', \'f_content\');">[img]</span>
					<span title="' . t('Миниатюра') . '" onclick="addSmile(\'' . $image . '\', \'f_content\');">[image]</span>
					
					<span title="' . t('Использовать как изображение записи') . '" onclick="addImgPage(\'' . $uploads_url . '/' . $file . '\');">+</span>
					';
					
					
		}
		
		
		$all_files_res .= '</div>'; 
		
	}
	
	
	echo $all_files_res . '<div class="break"></div>';
}

# end file