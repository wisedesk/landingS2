<?php
require_once $_SERVER['DOCUMENT_ROOT']."/lib/PHPMailer/src/PHPMailer.php";
require_once $_SERVER['DOCUMENT_ROOT']."/lib/PHPMailer/src/SMTP.php";

class SendMail {
	protected $mailer = null;

	// Instantiate a new PHPMailer 
	function __construct($charset = 'utf-8') {
		$this->mailer = new PHPMailer;

		$this->mailer->CharSet = "UTF-8";
		$this->mailer->Encoding = "base64";
		$this->mailer->setLanguage("ko");

		$this->setUseSmtpServer(true);
		$this->setSmtpServer("smtp.daum.net");
		$this->setSmtpPort(465);
		$this->setSmtpUser("tpist");
		$this->setSmtpPasswd("2qjsghkrdls");
		$this->setSmtpAuth(true);
		$this->setSmtpSecure("ssl");
		$this->setIsHtml(true);
	}

	function setUseSmtpServer($is_use_smtp) {
		if ($is_use_smtp) {
			// Tell PHPMailer to use SMTP
			$this->mailer->isSMTP();
		}
		else {
			$this->mailer->isMail();
		}
	}

	function setSmtpServer($smtpServer) {
		$this->mailer->Host = $smtpServer;
	}

	function setSmtpPort($smtpPort) {
		$this->mailer->Port = $smtpPort; //25, 465 or 587
	}
	
	function setSmtpUser($smtpUser) {
		$this->mailer->Username = $smtpUser;
	}
	
	function setSmtpPasswd($smtpPasswd) {
		$this->mailer->Password = $smtpPasswd;
	}

	function setSmtpAuth($is_auth) {
		$this->mailer->SMTPAuth = $is_auth;
	}

	function setSmtpSecure($smtpSecure) {
		$this->mailer->SMTPSecure = $smtpSecure;
	}

	function setIsHtml($isHTML) {
		$this->mailer->isHTML($isHTML);
	}

	function setFrom($email, $name = "") {
		$this->mailer->setFrom($email, $name);
	}

	function addTo($email, $name = "") {
		$this->mailer->addAddress($email, $name);
	}

    function addCc($email, $name = "") {
        $this->mailer->addCC($email, $name);
    }

    function addBcc($email, $name = "") {
        $this->mailer->addBCC($email, $name);
    }

	function setSubject($subject) {
		$this->mailer->Subject = $subject;
	}

	function addAttach($filename, $source) {
		// 첨부파일을 추가한다
		$fp = fopen($source, 'r');		// 소스파일을 연다
		if($fp) {
			$fBody = fread($fp, filesize($source));		// 파일의 내용을 읽어온다
			@fclose($fp);
			
			$this->Attach[$filename] = $fBody;			// Attach 배열에 담는다
		}
	}
	
	function setMailBody($body, $isUseHtml = true) { 
		if ($isUseHtml) {
			$this->mailer->Body = $body;
		}
		else {
			$this->mailer->Body = strip_tags($body);
		}
		$this->mailer->AltBody = strip_tags($body);
	}

	function setContents($mailContents, $isUseHtml = true) {
		$this->setMailBody($this->getContents($mailContents), $isUseHtml);
	}

	function getContents($mailContents) {
		$mailMessage = "";
		$mailMessage .= "<body style=\"margin:0px 0px;\">";
		$mailMessage .= "	<div style=\"width:100%; height:100%; background-color:#f1f1f1; padding:0px 0px;\">";
		$mailMessage .= "		<div style=\"text-align:center;\">";
		$mailMessage .= "			<div style=\"display:inline-block; margin:10px 10px;\">";
		$mailMessage .= "				<div style=\"text-align:left; padding-top:10px;\">";
		$mailMessage .= "					<img src=\"http://tp.wisedesk.net/images/wisedesk_h.png\" alt=\"WiseDesk\">";
		$mailMessage .= "					<div style=\"width:100%; font-size:16px; color:#1c75bc; text-align:center; display:inline-block; padding-top:5px; float:right;\">".$mailContents["subject"]."</div>";
		$mailMessage .= "				</div>";
		$mailMessage .= "				<div style=\"padding:20px 20px; background-color:#ffffff; border:1px solid #ffffff; border-radius:10px;\">";
		$mailMessage .= "					<div style=\"min-width:600px; min-height:200px; text-align:left; display:inline-block; font-size:13px;\">";
		$mailMessage .= "						".$mailContents["contents"];
		$mailMessage .= "					</div>";
		if ($mailContents["button_name"]) {
			$mailMessage .= "					<div style=\"padding:20px 10px; text-align:center;\">";
			$mailMessage .= "						<a href=\"".$mailContents["button_url"]."\" style=\"text-decoration:none;\">";
			$mailMessage .= "							<div style=\"background-color:#1874be; border:1px solid #1874be; border-radius:5px; color:#ffffff; font-size:20px; font-weight:700; padding:10px 50px; display:inline-block; cursor:pointer;\">";
			$mailMessage .= "								".$mailContents["button_name"];
			$mailMessage .= "							</div>";
			$mailMessage .= "						</a>";
			$mailMessage .= "					</div>";
		}
		$mailMessage .= "					<div style=\"padding:10px 10px 0px 10px; text-align:center; border-top:1px dashed #1874be; font-size:12px;\">";
		$mailMessage .= "						본 메일은 이메일 답장을 수신할 수 없습니다.";
		if ($mailContents["button_name"]) {
			$mailMessage .= "						<br />";
			$mailMessage .= "						자세한 내용 확인이나 답변은 위 ".$mailContents["button_name"]." 버튼을 눌러주세요.";
		}
		$mailMessage .= "					</div>";
		$mailMessage .= "				</div>";
		$mailMessage .= "				<div style=\"padding-top:10px; text-align:center; font-size:12px;\">";
		if ($mailContents["button_name"]) {
			$mailMessage .= "					메일 수신을 원치 않으시면 와이즈데스크 설정에서 이메일수신안함을 선택하시거나";
			$mailMessage .= "					<br />";
			$mailMessage .= "					확인/답변하기 버튼을 누르고 수신거부를 요청해 주세요.";
		}
		else {
			$mailMessage .= "					메일 수신을 원치 않으시면 와이즈데스크 설정에서";
			$mailMessage .= "					<br />";
			$mailMessage .= "					이메일수신안함을 선택하시거나 수신거부를 요청해 주세요.";
		}
		$mailMessage .= "				</div>";
		$mailMessage .= "			</div>";
		$mailMessage .= "		</div>";
		$mailMessage .= "	</div>";
		$mailMessage .= "</body>";

		return $mailMessage;
	}

	function send() {
		// tpist@daum.net로 숨은참조 발송
		$this->mailer->addBCC("tpist@daum.net", "와이즈데스크");

		$this->writeLog();

		$this->mailer->send();
	}

	function writeLog($logMsg) {
		//return;
		
		$logMessage = array();
		array_push($logMessage, $this->mailer->addrAppend("From", [[trim($this->mailer->From), $this->mailer->FromName]]));
		$to = $this->mailer->getToAddresses();
		if (count($to) > 0) {
			array_push($logMessage, $this->getAddress("To", $to));
		}
		$cc = $this->mailer->getCcAddresses();
		if (count($cc) > 0) {
			array_push($logMessage, $this->getAddress("Cc", $cc));
		}
		$bcc = $this->mailer->getBccAddresses();
		if (count($bcc) > 0) {
			array_push($logMessage, $this->getAddress("Bcc", $bcc));
		}
		array_push($logMessage, "subject : ".$this->mailer->Subject);
		array_push($logMessage, "[body]");
		array_push($logMessage, $this->mailer->Body);
		
		$system_root = $_SERVER['DOCUMENT_ROOT']."/..";
		
		$logTime = time();
		$logDate = date("Ymd", $logTime);
		$log_path = $system_root."/logs/".$logDate;
		if (!is_dir($log_path)) {
			$is_success = mkdir($log_path);
			chmod($log_path, 0777);
		}
		$logFileName = $log_path."/SendMail_".date("Ymd_H", $logTime).".log";
		$logFile = fopen($logFileName, "a");
		$lock = flock($logFile, LOCK_EX);
		if ($lock) {
			fwrite($logFile, "[".date("Y.m.d H:i:s", $logTime)."]");
			fwrite($logFile, "\n");
			fwrite($logFile, implode("\n", $logMessage));
			fwrite($logFile, "\n");
		}
		flock($logFile, LOCK_UN);
		fclose($logFile);
	}

	function getAddress($type, $addr) {
        $addresses = array();
        foreach ($addr as $address) {
	        array_push($addresses, $address[1]."<".$address[0].">");
        }

        return $type." : ".implode(", ", $addresses);
	}
}
?>
