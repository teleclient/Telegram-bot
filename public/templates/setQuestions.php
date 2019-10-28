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
            <a href="/?p=setInfo" class="admin-profile__href">Настройки</a>
        </div>
        <div class="admin-board">
            <div class="admin-board__title">Изменить опрос</div>
            <div class="admin-board__desc">Изменить данные о вопросе, данные обновляется автоматически</div>
            <form class="admin-board__form">
                <div class="admin-board__unit">
                    <label for="question">Заголовок:</label>
                    <input type="text" class="admin-board__input" id="question" value="<?php print $default['setQuestions']['qtitle']; ?>">
                </div>
                <div class="admin-board__unit">
                    <label for="question">Картинка:</label>
                    <input type="text" class="admin-board__input" id="picture" value="<?php print $default['setQuestions']['picture']; ?>">
                </div>
                <div class="admin-board__unit">
                    <label for="description">Описание:</label>
                    <textarea type="text" class="admin-board__input" id="description"><?php print $default['setQuestions']['qtext']; ?></textarea>
                </div>
                <div class="admin-board__unit">
                    <label for="question">Варианты ответов:</label>
                    <br>
                    <div class="admin-board__options">
                        <div class="admin-board__item admin-board__item--add">
                        <span class="admin-board__item--icon">&#128932;</span>
                            <input class="admin-board__item--name" placeholder="Ответ">
                        </div>
                    </div>
                    <!input type="submit" name="add_answer" class="admin-board__input" id="question">
                </div>
                <input type="submit" id="edit_question" class="admin-board__input" value="Изменить">
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
        <script>
        $(document).ready(function() {
            $(document).on("click", "#edit_question", function(e) {
                e.preventDefault();
                process();

                var units = [];
                $(".admin-board__options .admin-board__item:not(.admin-board__item--add)").each(function (index, unit) {
                    units[index] = {
                        id: index,
                        title: $(this).find(".admin-board__item--name").html(),
                        correct: $(this).attr("data-option-correct"),
                    };
                });

                $.ajax({
                    type: "POST",
                    url: "editdb.php",
                    data: "question=" + $("#question").val() + "&description=" + $("#description").val() + "&picture=" + $("#picture").val() + "&edit_question=true" + "&answers=" + JSON.stringify(units),
                    success: function (data) {
                        console.log(data);
                        process("result", true);
                    },
                });
            });

            var option_id = 1;
            function addBoardItem(context, title, correct, option_id) {
                $(context)
                    .parent()
                    .parent()
                    .append('<div class="admin-board__item" data-option-id="' + option_id + '" data-option-correct="' + correct + '"><span class="admin-board__item--icon admin-board__item--remove">&#9932;</span><span class="admin-board__item--name">' + title + '</span><span class="admin-board__item--icon admin-board__item--correct">Да</span></div>');
            }

            $(document).on("click", ".admin-board__item--add .admin-board__item--icon", function () {
                var title = $(this).parent().find(".admin-board__item--name").val();
                if (title == "") {
                    alert("Поле не должно быть пустым");
                    return;
                }
                addBoardItem(this, title, true, option_id);
                option_id++;
            });

            $(document).on("click", ".admin-board__item--remove", function () {
                $(this).parent().remove();
            });

            $(document).on("click", ".admin-board__item--correct", function () {
                var correct = $(this).parent().attr("data-option-correct");
                if (correct == "true") {

                    $(this)
                        .html("Нет")
                        .parent().attr("data-option-correct", false);
                } else {
                    $(this)
                        .html("Да")
                        .parent().attr("data-option-correct", true);
                }
            });
            
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