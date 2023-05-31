<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "connexion.php";

class User extends Singleton
{
    private $db = null;
    private $table = '';

    public function __construct(string $newHost, int $newPort, string $newDbname, string $newUser, string $newPassword, string $newTable, ?array $newOptions = [])
    {
        parent::setConfiguration($newHost, $newPort, $newDbname, $newUser, $newPassword, $newOptions);
        $this->db = parent::getPDO();
        $this->table = $newTable;
    }

    public function insert(array $post = []): int
    {
        if (empty($post)) {
            throw new Exception(__CLASS__ . ' : Le tableau ne doit pas être vide.');
        } else {
            $cols = array_keys($post);
            $vals = array_values($post);
            $params = array_fill(0, count($cols), '?');


            $sql = 'INSERT INTO ' . $this->table . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', $params) . ')';

            try {
                $qry = $this->db->prepare($sql);
                $qry->execute($vals);
                return $qry->rowCount();
            } catch (PDOException $err) {
                throw new Exception($err->getMessage());
            }
        }
    }

    public function delete(string $id): int
    {
        try {
            //Construit la requete SQL et delete la ligne
            $sql = 'DELETE FROM ' . $this->table . ' WHERE nom = :nom';
            $qry = $this->db->prepare($sql);
            $qry->execute(array(':nom' => $id));
            return $qry->rowCount();
        } catch (PDOException $err) {
            throw new Exception($err->getMessage());
        }
    }

    public function getByEmail(string $email)
    {
        try {
            $sql = 'SELECT * FROM ' . $this->table . ' WHERE email = :email';
            $qry = $this->db->prepare($sql);
            $qry->execute([':email' => $email]);
            return $qry->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $err) {
            throw new Exception($err->getMessage());
        }
    }

    public function sendEmail(string $email)
    {
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';

        $mail = new PHPMailer(true);

        $template = "template_mail.php";

        try {
            // Paramètres du serveur SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'newsletter.footwear@gmail.com'; // Votre adresse e-mail
            $mail->Password = 'ygvbxbqdkwncjyyw'; // Votre mot de passe
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            // Destinataire et expéditeur
            $mail->setFrom('newsletter.footwear@gmail.com'); // Votre adresse e-mail et votre nom
            $mail->addAddress($email);

            // Contenu de l'e-mail
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8'; // Spécifier l'encodage
            $mail->Subject = 'Cher nouveau membre de notre newsletter';
            $mail->Body = file_get_contents($template);

            $mail->send();
            echo "Email sent successfully.";
        } catch (Exception $e) {
            echo "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    }
}
