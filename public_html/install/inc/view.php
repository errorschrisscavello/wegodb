<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <title>WegoDB Installer</title>
    </head>
    <body>
        <h1><?php echo $heading; ?></h1>

        <?php foreach($messages as $message): ?>
            <p><?php echo $message; ?></p>
        <?php endforeach; ?>

        <?php foreach($errors as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>

        <div>
            <?php echo $content; ?>
        </div>
    </body>
</html>