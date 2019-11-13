<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Document</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700&display=swap&subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css?">
</head>
<body>
    <div class="admin-panel">
        <div class="admin-panel-bar">
            <div class="admin-profile">
                <div class="admin-profile__greating">
                    Здравствуйте
                    <span class="admin-profile__username"><?php print $auth->username; ?></span>
                </div>
            </div>
            <a href="/" class="admin-profile__href">Назад</a>
        </div>
        <div class="admin-board">
            <div class="admin-board__title">Настройки</div>
            <div class="admin-board__desc">Изменить общие дынные, требуется обновление данные у бота (<span class="admin-board__marked">/updateInfo</span>)</div>
            <form class="admin-board__form">
                <div class="admin-board__unit">
                    <label for="button_again">Кнопка ещё раз:</label>
                    <input type="text" class="admin-board__input" id="button_again" value="<?php print $default['setInfo']['button_again']; ?>">
                </div>
                <div class="admin-board__unit">
                    <label for="info_correct">Ответ (Правильный)</label>
                    <input type="text" class="admin-board__input" id="info_correct" value="<?php print $default['setInfo']['info_correct']; ?>">
                </div>
                <div class="admin-board__unit">
                    <label for="info_inCorrect">Ответ (НЕправильный)</label>
                    <input type="text" class="admin-board__input" id="info_inCorrect" value="<?php print $default['setInfo']['info_inCorrect']; ?>">
                </div>
                <div class="admin-board__unit">
                    <label for="start_before">Начальное сообщение (До)</label>
                    <div contenteditable="true" class="admin-board__input textarea" id="start_before"><?php print $default['setInfo']['start_before']; ?></div>
                </div>
                <div class="admin-board__unit">
                    <label for="start_after">Начальное сообщение (После)</label>
                    <div contenteditable="true" class="admin-board__input textarea" id="start_after"><?php print $default['setInfo']['start_after']; ?></div>
                </div>
                <input type="submit" id="edit_info" class="admin-board__input" value="Изменить">
                <!input type="submit" id="send_question" class="admin-board__input" value="Отправить">
            </form>
        </div>
        <div class="admin-panel__process">
            <span class="admin-panel__icon admin-panel__icon--circle"></span>
            <span class="admin-panel__text"></span>
        </div>
    </div>
    <footer>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="/assets/js/scripts.js"></script>
        <script>
        $(document).ready(function() {
            $(document).on("click", "#edit_info", function(e) {
                e.preventDefault();
                process();

                $.ajax({
                    type: "POST",
                    url: "editdb.php",
                    data: "button_again=" + $("#button_again").val() + "&start_before=" + $("#start_before").val() + "&start_after=" + $("#start_after").val() + "&info_correct=" + $("#info_correct").val() + "&info_inCorrect=" + $("#info_inCorrect").val() + "&edit_info=true",
                    success: function (data) {
                        console.log(data);
                        process("result", true);
                    },
                });
            });

            //$(document).on("focusout", ".textarea", function () {
                $(".textarea").each(function (index, unit) {
                    var textarea_html = $(unit).html().wrapBetween(["%", "%"], "marked");
                    $(unit).html(textarea_html);
                });
            //});
        });

        function process(option, hide = false) {
            $(".admin-panel__process").removeClass("admin-panel__process--result admin-panel__process--error");
            $(".admin-panel__icon").removeClass("admin-panel__icon--circle");
            $(".admin-panel__icon").html(null);
            if (option == "result") {
                $(".admin-panel__process")
                    .addClass("admin-panel__process--result")
                    .find(".admin-panel__text")
                    .html("Сохранения были успешно применены!");

                $(".admin-panel__process")
                    .find(".admin-panel__icon")
                    .html("&#10003;");
            } else if (option == "error") {
                $(".admin-panel__process")
                    .addClass("admin-panel__process--error")
                    .find(".admin-panel__text")
                    .html("Ошибка при сохранении (доп. в консоле)");

                $(".admin-panel__process")
                    .find(".admin-panel__icon")
                    .html("&#128938;");
            } else {
                $(".admin-panel__process").find(".admin-panel__text").html("Подождите идёт сохранение...");
                $(".admin-panel__icon").addClass("admin-panel__icon--circle");
            }
            $(".admin-panel__process").css({bottom: "-50px"});
            
            if (hide) {
                setTimeout(() => $(".admin-panel__process").css({bottom: 0}), 2000);
            }
        }
        </script>
    </footer>
</body>
</html>