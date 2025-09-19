-- 为reviews表添加service_id字段，允许通过订单ID或服务ID关联评价
ALTER TABLE reviews ADD COLUMN service_id INT NULL DEFAULT NULL;

-- 添加索引以提高查询性能
CREATE INDEX idx_service_id ON reviews(service_id);

-- 添加注释说明字段用途
ALTER TABLE reviews MODIFY COLUMN service_id INT NULL DEFAULT NULL COMMENT 'Optional service ID for direct service reviews';