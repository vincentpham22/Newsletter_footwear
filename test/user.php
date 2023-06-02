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

    public function delete(string $email): string
    {
        try {
            //Construit la requete SQL et delete la ligne
            $sql = 'DELETE FROM ' . $this->table . ' WHERE email = :email';
            $qry = $this->db->prepare($sql);
            $qry->execute(array(':email' => $email));
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
            $mail->Subject = 'Cher nouveau membre Footwear';
            $mail->Body = '<html><body style="background-color: #f2f2f2; font-family: Arial, sans-serif;">
            <div class="container" style="max-width: 600px; margin: 0 auto; padding: 20px; background-color: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                <h1 style="color: #333; text-align: center;">Merci de vous être inscrit à notre newsletter !</h1>
                <p class="success-message" style="margin-top: 20px; text-align: center; font-size: 18px; color: #4caf50;">Vous recevrez bientôt nos dernières actualités et offres spéciales.</p>
                <div class="btn" style="text-align: center; margin-top: 20px;">
                    <a href="http://localhost/wordpress1/boutique/" style="display: inline-block; background-color: black; color: #fff; padding: 10px; text-align: center; text-decoration: none;">Découvrez nos produits</a>
                </div>
                <div class="lien" style="text-align: center; margin-top: 10px;">
                    <p><a href="localhost/test/desinscrire.php">Se désinscrire</a></p>
                </div>
            </div>
        </body></html>';

            $mail->send();
        } catch (Exception $e) {
            echo "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    }

    public function sendEmail2(string $email)
    {
        require_once 'PHPMailer/src/PHPMailer.php';
        require_once 'PHPMailer/src/SMTP.php';
        require_once 'PHPMailer/src/Exception.php';

        $mail = new PHPMailer(true);

        $template = "template_desinscrire.php";

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
            $mail->Subject = 'Comfirmation de désinscription à la newsletter';
            $mail->Body = file_get_contents($template);

            $mail->send();
        } catch (Exception $e) {
            echo "Failed to send email. Error: " . $mail->ErrorInfo;
        }
    }
}
