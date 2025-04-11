local redis = require "resty.redis"
local cjson = require "cjson"  -- Подключаем библиотеку для работы с JSON
local red = redis:new()  -- Создаем новый экземпляр клиента Redis
-- Устанавливаем параметры подключения
local host = "172.18.0.1"  -- Замените на ваш хост
local port = 6379  -- Замените на ваш порт
local password = "A3Ox_z_vUgcMkgVYIW5nUgQPKccgARXxwgNGpo8tag8"  -- Замените на ваш пароль

-- Подключаемся к Redis
red:set_timeout(1000)

local ok, err = red:connect(host, port)  -- Подключаемся к Redis
if not ok then
    ngx.say("Не удалось подключиться к Redis: ", err)  -- Обработка ошибки подключения
    return
end

-- Аутентификация
if password then
    local res, err = red:auth(password)  -- Аутентификация с паролем
    if not res then
        ngx.say("Ошибка аутентификации: ", err)  -- Обработка ошибки аутентификации
        return
    end
end

-- Получаем сериализованные данные из Redis
local exists = red:exists("main_categories")
if exists == 0 then
    -- Сделать редирект на категорию
    ngx.header['Content-Type'] = 'text/plain; charset=utf-8'
    ngx.say("Ключ не существует")
    return
end

local serializedData, err = red:get("main_categories")  -- Замените на ваш ключ

-- Закрываем соединение с Redis
red:close()

function extract_json(serialized)
    local start_pos = serialized:find('{"')
    local end_pos = serialized:find('}"', start_pos)

    return serialized:sub(start_pos, end_pos)
end

local json_data, err = extract_json(serializedData)

if json_data then
    ngx.header['Content-Type'] = 'application/json; charset=utf-8'
    ngx.say(json_data)
else
    ngx.header['Content-Type'] = 'text/plain; charset=utf-8'
    ngx.say("Ошибка: " .. err)
end
