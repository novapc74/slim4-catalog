local cjson = require "cjson"  -- Подключаем библиотеку cjson

local my_var = ngx.var.my_var  -- Получаем значение переменной Nginx

-- Создаем таблицу для JSON
local json_table = {
    message = my_var,
    data = {
        message = "try again :)"
        }
}

-- Сериализуем таблицу в JSON
local json_response = cjson.encode(json_table)

-- Отправляем JSON-ответ
ngx.say(json_response)
