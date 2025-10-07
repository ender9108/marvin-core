-- Table métriques (Timescale)
CREATE TABLE IF NOT EXISTS domotic_device_metric (
    device_id UUID NOT NULL,
    metric TEXT NOT NULL,
    recorded_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    value DOUBLE PRECISION NOT NULL,
    PRIMARY KEY (device_id, metric, recorded_at)
);
SELECT create_hypertable('domotic_device_metric', 'recorded_at', if_not_exists => TRUE);


CREATE INDEX IF NOT EXISTS idx_domotic_device_metric_recorded_at ON domotic_device_metric (recorded_at DESC);
CREATE INDEX IF NOT EXISTS idx_domotic_device_metric_dev_metric ON domotic_device_metric (device_id, metric);


-- Rétention et compression métriques
SELECT add_retention_policy('domotic_device_metric', INTERVAL '24 months', if_not_exists => TRUE);
ALTER TABLE domotic_device_metric SET (
    timescaledb.compress,
    timescaledb.compress_segmentby = 'device_id,metric'
);
SELECT add_compression_policy('domotic_device_metric', INTERVAL '30 days', if_not_exists => TRUE);


-- Table logs courte (2–3 jours)
CREATE TABLE IF NOT EXISTS system_log (
    id BIGSERIAL PRIMARY KEY,
    channel VARCHAR(64),
    level VARCHAR(16),
    message TEXT,
    context JSONB DEFAULT '{}'::jsonb,
    extra JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMPTZ DEFAULT NOW()
);
CREATE INDEX IF NOT EXISTS idx_system_log_created_at ON system_log (created_at DESC);
CREATE INDEX IF NOT EXISTS idx_system_log_level ON system_log (level);
CREATE INDEX IF NOT EXISTS idx_system_log_channel ON system_log (channel);
