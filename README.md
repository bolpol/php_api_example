
	# Describe

	eth_wallet - 0x0…123
	auth_token - ключ которым подписуються запросы write/read
	nonce - числовое значение нужно посылать на один раз больше чем в предыдущий раз, отсчет 	вести от 1 (если не валидно будет вызванна ошибка что не совпадает nonce id или hmac)
	mode - “write” или “read”

	K59s3xUr6w3ne42dAX2XyT6uVy2uD9DU  => write key
	h2UB4p5h3duk6u32z8SY5KeFec4xEB6a   => read key

	write key - ключ овнера
	read key - используеться апи для обновления баланса бонусного кошелька

	Создает нового юзера по адрессу эфира
	 GET /api/wallet/create
	 POST - eth_wallet, auth_token, nonce
	 PERMISSION - wright

	Получаем информацию все адресы и балансы по юзеру
	 GET /api/wallet/get_user_info=eth_wallet

	Получаем биткойн адресс для пополнения
	 GET /api/wallet/get_btc_address=eth_wallet
	RewriteRule ^api/([^/]*)/get_btc_address=([^/]*)$ /?api=$1&get_btc_address=$2 [L]

	Получаем биткойн баланс из базы (вообще нужно тянуть из блокчейна)
	 GET /api/wallet/get_btc_balance=eth_wallet

	Получаем бонусный адресс
	 GET /api/wallet/get_bonus_address=eth_wallet

	Получаем бонусный баланс
	 GET /api/wallet/get_bonus_balance=eth_wallet

	Получаем бонусный баланс
	 GET /api/wallet/update_bonus_balance
	 POST - eth_wallet, new_balance, auth_token, nonce
	 PERMISSION - read

	Получаем приватный ключ от адреса для пополнения
	 GET /api/wallet/get_btc_priv_key=btc_base_address
	 POST - auth_token, nonce
	 PERMISSION - wright

	Получаем приватный ключ от адреса для пополнения
	 GET /api/wallet/get_bonus_priv_key=btc_bonus_address
	 POST - auth_token, nonce
	 PERMISSION - wright

	Удаляем пользователя за его адресом eth_wallet
	 GET /api/wallet/delete_by_address=eth_wallet
	 POST - auth_token, nonce
	 PERMISSION - wright

	Обновляем ключ юзера, nonce=1
	 GET - /api/user/update_user
	 POST - auth_token, nonce
	 PERMISSION - wright


	Создает нового пользователя
	 GET - /api/user/gen_user
	 POST - auth_token, nonce, mode
	 PERMISSION - wright

	*Бонусный адрес тоже валидный адрес в сети биткойна, на случай того если на него юзер переведет случайно свои средства.

	Пример запроса
	Test url: /api/wallet/get_user_info=0x
	Result: Json-объект {"status":"Error","message":"User not found","result":[]}

	Вид ответа от сервера
	{"status":"Ok","message":"Success message ","result":[]}
	{"status":"Error","message":"Error message","result":[]}

	Генерация auth токена


	// код на php

	$test = [];

	$test['id'] = "1"; // id ключа из базы
	$test['nonce'] = 1; // nonce
	$test['sharedKey'] = "someVerySecureString"; // ключ - token
	$test['hmacKey'] = hash_hmac ( 'sha256', $test['sharedKey'] , $test['nonce'] );
	$test['hmac'] = hash_hmac ( 'sha256', $test['userId'] , $test['hmacKey'] );

	auth_token  =  $test['hmac']
