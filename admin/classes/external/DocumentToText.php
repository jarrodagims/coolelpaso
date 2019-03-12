<?php
class DocumentToText{
    private $filename;

    public function __construct($filePath) {
        $this->filename = $filePath;
    }
	
	/* Filters data to be compliant with dom file input inline javascript array*/
    private function filter($data){
		$with = array("&#44;", "&#39;", "&#34;");
		$replace = array(",", "'", "\"");
		$data = str_replace($replace, $with, $data);
		$data = trim(preg_replace('/\s\s+/', '', $data));
		$data = preg_replace('~[\r\n]+~', '', $data);
		if(strlen($data) > 4995){
			substr($data, 0, 4955);
			$data .= '... Please download to view entire file.';
		}elseif(strlen($data) < 1){
			$data .= 'No preview data available...<br />Please download to view entire file.';
		}
        return $data;
    }
	
	/* Text from Pain Text Format*/
    private function text_to_text(){
        $outtext = file_get_contents($this->filename);
        return $this->filter($outtext);
    }
	
	/* Text from .doc Format*/
    private function doc_to_text(){
		$fileHandle = fopen( $this->filename, "r" );
		$headers = fread($fileHandle, 0xA00);
		# 1 = (ord(n)*1) ; Document has from 0 to 255 characters
		$n1 = (ord($headers[0x21C]) - 1);
		# 1 = ((ord(n)-8)*256) ; Document has from 256 to 63743 characters
		$n2 = ((ord($headers[0x21D]) - 8) * 256);
		# 1 = ((ord(n)*256)*256) ; Document has from 63744 to 16775423 characters
		$n3 = ((ord($headers[0x21E]) * 256) * 256);
		# (((ord(n)*256)*256)*256) ; Document has from 16775424 to 4294965504 characters
		$n4 = (((ord($headers[0x21F]) * 256) * 256) * 256);
		# Total length of text in the document
		$textLength = ($n1 + $n2 + $n3 + $n4);
		$extracted_plaintext = fread($fileHandle, $textLength);
		return $this->filter(utf8_encode(nl2br($extracted_plaintext)));
    }

	/* Text from .docx Format*/
    private function docx_to_text(){
        $striped_content = '';
        $content = '';
        $zip = zip_open($this->filename);
        if (!$zip || is_numeric($zip)) return false;
        while ($zip_entry = zip_read($zip)) {
            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;
            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);
        return $this->filter($striped_content);
    }
	
	/* Text from .odt Format*/
	private function odt_to_text(){
		$xml_filename = "content.xml"; //content file name
		$zip_handle = new ZipArchive;
		$output_text = "";
		if(true === $zip_handle->open($this->filename)){
			if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
				$xml_datas = $zip_handle->getFromIndex($xml_index);
				$xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				$output_text = strip_tags($xml_handle->saveXML());
			}else{
				$output_text .="";
			}
			$zip_handle->close();
		}else{
		$output_text .="";
		}
		return $this->filter($output_text);
	}
	
	

	/* Text from .xlsx Format*/
	private function xlsx_to_text(){
		$xml_filename = "xl/sharedStrings.xml"; //content file name
		$zip_handle = new ZipArchive;
		$output_text = "";
		if(true === $zip_handle->open($this->filename)){
			if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
				$xml_data = $zip_handle->getFromIndex($xml_index);
				$xml_handle = DOMDocument::loadXML($xml_data, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				$output_text = strip_tags(str_replace("</si>","</si>, ",$xml_handle->saveXML()));
			}else{
				$output_text .="zip handle failed";
			}
			$zip_handle->close();
		}else{
			$output_text .="failed to open file";
		}
		return $this->filter(rtrim($output_text,", "));
	}

	/* Text from .ppt Format*/
	private function pptx_to_text(){
		$zip_handle = new ZipArchive;
		$output_text = "";
		if(true === $zip_handle->open($this->filename)){
			$slide_number = 1; //loop through slide files
			while(($xml_index = $zip_handle->locateName("ppt/slides/slide".$slide_number.".xml")) !== false){
				$xml_datas = $zip_handle->getFromIndex($xml_index);
				$xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
				$output_text .= strip_tags($xml_handle->saveXML());
				$slide_number++;
			}
			if($slide_number == 1){
				$output_text .="";
			}
			$zip_handle->close();
		}else{
		$output_text .="";
		}
		return $this->filter($output_text);
	}

	/* Text from .rtf Format*/
	private function rtf_isPlainText($s){
		$arrfailAt = array("*", "fonttbl", "colortbl", "datastore", "themedata");
		for ($i = 0; $i < count($arrfailAt); $i++)
			if (!empty($s[$arrfailAt[$i]])) return false;
		return true;
	}
	
	private function rtf_to_text(){
		$text = file_get_contents($this->filename);
		if (!strlen($text)){
			return "";
		}
		$document = "";
		$stack = array();
		$j = -1;
		for ($i = 0, $len = strlen($text); $i < $len; $i++){
			$c = $text[$i];
			switch ($c) {
				case "\\":
					$nc = $text[$i + 1];
					if($nc == '\\' && $this->rtf_isPlainText($stack[$j])){
						$document .= '\\';
					}elseif($nc == '~' && $this->rtf_isPlainText($stack[$j])){
						$document .= ' ';
					}elseif($nc == '_' && $this->rtf_isPlainText($stack[$j])){
						$document .= '-';
					}elseif($nc == '*'){
						$stack[$j]["*"] = true;
					}elseif($nc == "'"){
						$hex = substr($text, $i + 2, 2);
						if ($this->rtf_isPlainText($stack[$j])){
							$document .= html_entity_decode("&#".hexdec($hex).";");
						}
						$i += 2;
					}elseif($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z'){
						$word = "";
						$param = null;
						for($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++){
							$nc = $text[$k];
							if($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z'){
								if(empty($param)){
									$word .= $nc;
								}else{
									break;
								}
							}elseif($nc >= '0' && $nc <= '9'){
								$param .= $nc;
							}elseif($nc == '-'){
								if (empty($param)){
									$param .= $nc;
								}else{
									break;
								}
							}else{
								break;
							}
						}
						$i += $m - 1;
						$toText = "";
						switch (strtolower($word)){
							case "u":
								$toText .= html_entity_decode("&#x".dechex($param).";");
								$ucDelta = @$stack[$j]["uc"];
								if ($ucDelta > 0){
									$i += $ucDelta;
								}
							break;
							case "par": case "page": case "column": case "line": case "lbr":
								$toText .= "\n";
							break;
							case "emspace": case "enspace": case "qmspace":
								$toText .= " ";
							break;
							case "tab": $toText .= "\t"; break;
							case "chdate": $toText .= date("m.d.Y"); break;
							case "chdpl": $toText .= date("l, j F Y"); break;
							case "chdpa": $toText .= date("D, j M Y"); break;
							case "chtime": $toText .= date("H:i:s"); break;
							case "emdash": $toText .= html_entity_decode("&mdash;"); break;
							case "endash": $toText .= html_entity_decode("&ndash;"); break;
							case "bullet": $toText .= html_entity_decode("&#149;"); break;
							case "lquote": $toText .= html_entity_decode("&lsquo;"); break;
							case "rquote": $toText .= html_entity_decode("&rsquo;"); break;
							case "ldblquote": $toText .= html_entity_decode("&laquo;"); break;
							case "rdblquote": $toText .= html_entity_decode("&raquo;"); break;
							default:
								$stack[$j][strtolower($word)] = empty($param) ? true : $param;
							break;
						}
						if ($this->rtf_isPlainText($stack[$j])){
							$document .= $toText;
						}
					}
					$i++;
				break;
				case "{":
					array_push($stack, $stack[$j++]);
				break;
				case "}":
					array_pop($stack);
					$j--;
				break;
				case '\0': case '\r': case '\f': case '\n': break;
				default:
					if ($this->rtf_isPlainText($stack[$j]))
						$document .= $c;
				break;
			}
		}
		return $this->filter($document);
	}
	
    public function convertToText() {
        if(isset($this->filename) && !file_exists($this->filename)) {
            return "File Does Not Exist";
        }
        $fileArray = pathinfo($this->filename);
        $file_ext  = $fileArray['extension'];
		switch ($file_ext){
			case "txt":
				return $this->text_to_text();	
				break;
			case "csv":
				return $this->text_to_text();	
				break;
			case "doc":
				return $this->doc_to_text();	
				break;
			case "docx":
				return $this->docx_to_text();
				break;
			case "xlsx":
				return $this->xlsx_to_text();	
				break;
			case "pptx":
				return $this->pptx_to_text();
				break;
			case "rtf":
				return $this->rtf_to_text();	
				break;
			default:
				return "Invalid File Type";
		}
    }
}
?>