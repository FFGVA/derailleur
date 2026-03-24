-- Drop chef_peloton_id from events table, audit table, and triggers
-- The column is replaced by the event_chef pivot table (multiple cheffes per event).
-- Run AFTER deploying the code that uses event_chef exclusively.
-- Date: 2026-03-24

-- 1. Drop column from events
ALTER TABLE events DROP FOREIGN KEY events_chef_peloton_id_foreign;
ALTER TABLE events DROP COLUMN chef_peloton_id;

-- 2. Drop column from audit table
ALTER TABLE events_audit DROP COLUMN chef_peloton_id;

-- 3. Recreate triggers without chef_peloton_id
DROP TRIGGER IF EXISTS events_before_update;
DROP TRIGGER IF EXISTS events_before_delete;

DELIMITER //

CREATE TRIGGER events_before_update BEFORE UPDATE ON events FOR EACH ROW
BEGIN
    INSERT INTO events_audit (audit_action, audit_user_id, id, event_type, title, description, location, starts_at, ends_at, max_participants, price, price_non_member, statuscode, strava_event_id, strava_route_id, gpx_file, modified_by_id, updated_at, deleted_at)
    VALUES ('U', @current_user_id, OLD.id, OLD.event_type, OLD.title, OLD.description, OLD.location, OLD.starts_at, OLD.ends_at, OLD.max_participants, OLD.price, OLD.price_non_member, OLD.statuscode, OLD.strava_event_id, OLD.strava_route_id, OLD.gpx_file, OLD.modified_by_id, OLD.updated_at, OLD.deleted_at);
END//

CREATE TRIGGER events_before_delete BEFORE DELETE ON events FOR EACH ROW
BEGIN
    INSERT INTO events_audit (audit_action, audit_user_id, id, event_type, title, description, location, starts_at, ends_at, max_participants, price, price_non_member, statuscode, strava_event_id, strava_route_id, gpx_file, modified_by_id, updated_at, deleted_at)
    VALUES ('D', @current_user_id, OLD.id, OLD.event_type, OLD.title, OLD.description, OLD.location, OLD.starts_at, OLD.ends_at, OLD.max_participants, OLD.price, OLD.price_non_member, OLD.statuscode, OLD.strava_event_id, OLD.strava_route_id, OLD.gpx_file, OLD.modified_by_id, OLD.updated_at, OLD.deleted_at);
END//

DELIMITER ;
