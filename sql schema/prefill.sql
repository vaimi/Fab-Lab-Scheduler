INSERT INTO `aauth_groups` VALUES ('1', 'Admin', 'Super Admin Group');
INSERT INTO `aauth_groups` VALUES ('2', 'Public', 'Public Access Group');
INSERT INTO `aauth_groups` VALUES ('3', 'University of Oulu', 'Employees and students of University of Oulu');

INSERT INTO `aauth_users` VALUES ('1', 'admin@example.com', 'dd5073c93fb477a167fd69072e95455834acd93df8fed41a2c468c45b394bfe3', 'Admin', '0', null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null);

INSERT INTO `aauth_user_to_group` VALUES ('1', '1');
INSERT INTO `aauth_user_to_group` VALUES ('1', '3');