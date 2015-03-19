<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <title>WegoDB Installer</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

        <style>
            main{
                max-width: 768px;
            }
            main h1{
                text-align: center;
            }
            main section{
                max-width: 512px;
            }
            main .alerts{
                max-width: 396px;
            }
            main,
            section,
            .alerts{
                margin: 0 auto;
            }
        </style>

    </head>
    <body>
        <main>
            <header>
                <h1><?php echo $heading; ?></h1>

                <div class="alerts">
                    <?php foreach($infos as $info): ?>
                        <div class="alert alert-info">
                            <?php echo $info; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($messages as $message): ?>
                        <div class="alert alert-success">
                            <?php echo $message; ?>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($errors as $error): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </header>

            <section>
                <?php echo $content; ?>
            </section>
        </main>
    </body>
</html>