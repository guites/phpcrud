###Fontes:
- [https://www.techiediaries.com/php-rest-api/](https://www.techiediaries.com/php-rest-api/)
- [https://github.com/devcoder-xyz/php-dotenv](https://github.com/devcoder-xyz/php-dotenv)
- [https://github.com/tutsplus/how-to-upload-a-file-in-php-with-example/blob/master/upload.php](https://github.com/tutsplus/how-to-upload-a-file-in-php-with-example/blob/master/upload.php)

#### Requerimentos:
versão do php > 7.1 

##### Para usar o .env e realizar login
```bash
mv example.env ../.env
```
```sql
CREATE TABLE 'Usuario' (
  'idUsuario' int NOT NULL AUTO_INCREMENT,
  'nmUsuario' varchar(50) NOT NULL,
  'pwdUsuario' varchar(255) NOT NULL,
  'emailUsuario' varchar(255) NOT NULL,
  'created_at' datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ('idUsuario'),
  UNIQUE KEY 'nmUsuario' ('nmUsuario'),
  UNIQUE KEY 'emailUsuario' ('emailUsuario')
);
```
