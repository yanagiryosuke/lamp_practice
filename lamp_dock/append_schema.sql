CREATE TABLE `purchase_histories` (
    `purchase_id` int(11) NOT NULL,
    `user_id` int(11),
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `purchase_details` (
    `detail_id` int(11) NOT NULL,
    `purchase_id` int(11) NOT NULL,
    `item_id` int(11),
    `item_name` varchar(100) NOT NULL,
    `item_price` int(11) NOT NULL,
    `item_amount` int(11) NOT NULL,
    `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `purchase_histories`
    ADD PRIMARY KEY (`purchase_id`),
    ADD KEY `user_id` (`user_id`);

ALTER TABLE `purchase_details`
    ADD PRIMARY KEY (`detail_id`),
    ADD KEY `purchase_id` (`purchase_id`),
    ADD KEY `item_id` (`item_id`);

ALTER TABLE `purchase_histories`
    MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchase_details`
    MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchase_histories`
    ADD CONSTRAINT `purchase_histories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `purchase_details`
    ADD CONSTRAINT `purchase_details_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase_histories` (`purchase_id`),
    ADD CONSTRAINT `purchase_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE SET NULL;