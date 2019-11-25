<?
	$system_root = $_SERVER['DOCUMENT_ROOT'];

	session_start();

	// 라이브러리 함수 파일 인크루드
	require_once $system_root."/lib/SendMail.class.php";

	$mailMessage = "";
	$mailMessage .= "회사명 : ".$_POST["company_name"];
	$mailMessage .= "\n";
	$mailMessage .= "이름 : ".$_POST["customer_name"];
	$mailMessage .= "\n";
	$mailMessage .= "전화번호 : ".$_POST["phone"];
	$mailMessage .= "\n";
	$mailMessage .= "이메일 : ".$_POST["email"];
	if ($_POST["inquiry_contents"]) {
		$mailMessage .= "\n";
		$mailMessage .= "문의내용 : ";
		$mailMessage .= "\n";
		$mailMessage .= $_POST["inquiry_contents"];
	}

	$mail = new SendMail();
	
	$mail->setFrom("tpist@daum.net", $_POST["company_name"]." / ".$_POST["customer_name"]);
	$mail->addTo("tpist@daum.net", "WD도입문의");
	$mail->setSubject("와이즈데스크 도입문의 [".$_POST["company_name"]."]");
	$mail->setMailBody(nl2br($mailMessage), true);
	$mail->send();

	$res = array();
	$res["is_success"] = "Y";

	$response = json_encode($res, JSON_UNESCAPED_UNICODE);
	echo($response);
?>