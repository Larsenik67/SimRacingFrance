<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220315082212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, jeu_id INT NOT NULL, team_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, nb_place INT NOT NULL, INDEX IDX_B26681E8C9E392E (jeu_id), INDEX IDX_B26681E296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jeu (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, icone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_prive (id INT AUTO_INCREMENT NOT NULL, destinataire_id INT NOT NULL, expediteur_id INT NOT NULL, objet VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_time DATETIME NOT NULL, INDEX IDX_2DB3B26A4F84F6E (destinataire_id), INDEX IDX_2DB3B2610335F61 (expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, sujet_id INT NOT NULL, user_id INT NOT NULL, contenu LONGTEXT NOT NULL, date_time DATETIME NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_DB021E967C4D497E (sujet_id), INDEX IDX_DB021E96A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse_prive (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message_prive_id INT NOT NULL, message LONGTEXT NOT NULL, date_time DATETIME NOT NULL, INDEX IDX_1294BFAEA76ED395 (user_id), INDEX IDX_1294BFAE77321B04 (message_prive_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sujet (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, date_time DATETIME NOT NULL, statut TINYINT(1) NOT NULL, closed TINYINT(1) NOT NULL, INDEX IDX_2E13599D296CD8AE (team_id), INDEX IDX_2E13599DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team_jeu (team_id INT NOT NULL, jeu_id INT NOT NULL, INDEX IDX_D8770711296CD8AE (team_id), INDEX IDX_D87707118C9E392E (jeu_id), PRIMARY KEY(team_id, jeu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, team_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, role_team JSON DEFAULT NULL, statut TINYINT(1) NOT NULL, ban DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649296CD8AE (team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_evenement (user_id INT NOT NULL, evenement_id INT NOT NULL, INDEX IDX_BC6E5FAA76ED395 (user_id), INDEX IDX_BC6E5FAFD02F13 (evenement_id), PRIMARY KEY(user_id, evenement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_jeu (user_id INT NOT NULL, jeu_id INT NOT NULL, INDEX IDX_69F2EC3EA76ED395 (user_id), INDEX IDX_69F2EC3E8C9E392E (jeu_id), PRIMARY KEY(user_id, jeu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id)');
        $this->addSql('ALTER TABLE evenement ADD CONSTRAINT FK_B26681E296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE message_prive ADD CONSTRAINT FK_2DB3B26A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_prive ADD CONSTRAINT FK_2DB3B2610335F61 FOREIGN KEY (expediteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E967C4D497E FOREIGN KEY (sujet_id) REFERENCES sujet (id)');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reponse_prive ADD CONSTRAINT FK_1294BFAEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reponse_prive ADD CONSTRAINT FK_1294BFAE77321B04 FOREIGN KEY (message_prive_id) REFERENCES message_prive (id)');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599D296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE sujet ADD CONSTRAINT FK_2E13599DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE team_jeu ADD CONSTRAINT FK_D8770711296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE team_jeu ADD CONSTRAINT FK_D87707118C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE user_evenement ADD CONSTRAINT FK_BC6E5FAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_evenement ADD CONSTRAINT FK_BC6E5FAFD02F13 FOREIGN KEY (evenement_id) REFERENCES evenement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_jeu ADD CONSTRAINT FK_69F2EC3EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_jeu ADD CONSTRAINT FK_69F2EC3E8C9E392E FOREIGN KEY (jeu_id) REFERENCES jeu (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_evenement DROP FOREIGN KEY FK_BC6E5FAFD02F13');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E8C9E392E');
        $this->addSql('ALTER TABLE team_jeu DROP FOREIGN KEY FK_D87707118C9E392E');
        $this->addSql('ALTER TABLE user_jeu DROP FOREIGN KEY FK_69F2EC3E8C9E392E');
        $this->addSql('ALTER TABLE reponse_prive DROP FOREIGN KEY FK_1294BFAE77321B04');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E967C4D497E');
        $this->addSql('ALTER TABLE evenement DROP FOREIGN KEY FK_B26681E296CD8AE');
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599D296CD8AE');
        $this->addSql('ALTER TABLE team_jeu DROP FOREIGN KEY FK_D8770711296CD8AE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649296CD8AE');
        $this->addSql('ALTER TABLE message_prive DROP FOREIGN KEY FK_2DB3B26A4F84F6E');
        $this->addSql('ALTER TABLE message_prive DROP FOREIGN KEY FK_2DB3B2610335F61');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96A76ED395');
        $this->addSql('ALTER TABLE reponse_prive DROP FOREIGN KEY FK_1294BFAEA76ED395');
        $this->addSql('ALTER TABLE sujet DROP FOREIGN KEY FK_2E13599DA76ED395');
        $this->addSql('ALTER TABLE user_evenement DROP FOREIGN KEY FK_BC6E5FAA76ED395');
        $this->addSql('ALTER TABLE user_jeu DROP FOREIGN KEY FK_69F2EC3EA76ED395');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE jeu');
        $this->addSql('DROP TABLE message_prive');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE reponse_prive');
        $this->addSql('DROP TABLE sujet');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_jeu');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_evenement');
        $this->addSql('DROP TABLE user_jeu');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
