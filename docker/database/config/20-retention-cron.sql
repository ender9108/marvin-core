-- Option A : pg_cron pour nettoyer les logs (si extension dispo)
-- Si l'image prend en charge pg_cron, active-la (selon build) :
CREATE EXTENSION IF NOT EXISTS pg_cron;
SELECT cron.schedule('log_cleanup_daily', '0 3 * * *', $$
DELETE FROM system_marvin_log WHERE created_at < NOW() - INTERVAL '3 days';
$$);


-- Option B : fonction SQL + policy Timescale (alternative portable)
-- CREATE OR REPLACE FUNCTION purge_old_logs() RETURNS void LANGUAGE plpgsql AS $$
-- BEGIN
-- DELETE FROM system_marvin_log WHERE created_at < NOW() - INTERVAL '3 days';
-- END;$$;
