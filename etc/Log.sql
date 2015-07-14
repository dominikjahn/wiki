	SELECT `log`.`timestamp` AS `timestamp`, `log`.`type` AS `type`, `performer`.`loginname` AS `user`, 'user' AS `object_type`, `log`.`object_id` AS object_id, `object`.`loginname` AS `object_text`
	FROM `log`
	INNER JOIN `user` AS `performer` USING (user_id)
	INNER JOIN `user` AS object ON `object`.`user_id` = `log`.`object_id`
	WHERE `log`.`object_table` = 'user'
UNION ALL
	SELECT `log`.`timestamp`, `log`.`type`, `performer`.`loginname`, 'page', `log`.`object_id`, `object`.`name`
	FROM `log`
	INNER JOIN `user` AS `performer` USING (user_id)
	INNER JOIN `page` AS object ON `object`.`page_id` = `log`.`object_id`
	WHERE `log`.`object_table` = 'page'
ORDER BY `timestamp`