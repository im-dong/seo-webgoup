-- 删除users表中的avatar字段，因为系统已经统一使用profile_image_url字段
-- 执行时间：2025-01-20
-- 风险评估：低风险，所有业务逻辑已使用profile_image_url

-- 1. 先备份数据（虽然所有都是默认值）
CREATE TABLE users_backup_avatar LIKE users;
INSERT INTO users_backup_avatar SELECT * FROM users;

-- 2. 验证没有业务依赖avatar字段
-- 检查是否有代码直接引用avatar字段（已在代码层面确认）

-- 3. 删除avatar字段
ALTER TABLE users DROP COLUMN avatar;

-- 4. 验证删除结果
-- SHOW COLUMNS FROM users;

-- 5. 清理备份表（可选执行）
-- DROP TABLE users_backup_avatar;

-- 注意：执行前请确保：
-- 1. 已备份重要数据
-- 2. 系统不在维护期间
-- 3. 有回滚方案（通过备份表）