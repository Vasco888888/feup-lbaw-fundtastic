CREATE SCHEMA IF NOT EXISTS lbaw2532;
SET search_path TO lbaw2532;

-- CLEAN PREVIOUS SCHEMA

DROP TABLE IF EXISTS 
    notification_collaboration_request,
    notification_campaign_update,
    notification_comment,
    notification_contribution,
    notification,
    campaign_update,
    media,
    report,
    comment,
    donation,
    collaboration_request,
    user_follows_campaign,
    campaign_collaborators,
    campaign,
    category,
    unban_appeal,
    password_reset_tokens,
    "user",
    admin
CASCADE;

DROP DOMAIN IF EXISTS Status, ReportStatus, MediaTypes, AppealStatus, CollaborationRequestStatus CASCADE;

DROP FUNCTION IF EXISTS prevent_campaign_edit() CASCADE;
DROP FUNCTION IF EXISTS prevent_self_donation() CASCADE;
DROP FUNCTION IF EXISTS validate_campaign_dates() CASCADE;
DROP FUNCTION IF EXISTS adjust_campaign_amount_and_status() CASCADE;
DROP FUNCTION IF EXISTS prevent_donation_to_completed_campaign() CASCADE;
DROP FUNCTION IF EXISTS set_campaign_status_completed() CASCADE;

-- DOMAIN DEFINITIONS

CREATE DOMAIN Status AS TEXT CHECK (VALUE IN ('active', 'completed', 'suspended'));
CREATE DOMAIN ReportStatus AS TEXT CHECK (VALUE IN ('open', 'resolved'));
CREATE DOMAIN MediaTypes AS TEXT CHECK (VALUE IN ('image', 'video', 'file'));
CREATE DOMAIN AppealStatus AS TEXT CHECK (VALUE IN ('pending', 'accepted', 'rejected'));

-- USER AND ADMIN TABLES

CREATE TABLE "user" (
    user_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email TEXT NOT NULL UNIQUE,
    password TEXT,
    name TEXT NOT NULL,
    bio TEXT,
    banned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_media_id INTEGER,
    external_profile_image TEXT,
    remember_token VARCHAR(100),
    google_id VARCHAR
);

CREATE TABLE admin (
    admin_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    name TEXT NOT NULL,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    profile_media_id INTEGER
);

-- PASSWORD RESET TOKENS TABLE (for Laravel password reset)

CREATE TABLE password_reset_tokens (
    email TEXT PRIMARY KEY,
    token TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CATEGORY TABLE

CREATE TABLE category (
    category_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL UNIQUE,
    description TEXT
);

-- CAMPAIGN TABLE

CREATE TABLE campaign (
    campaign_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    goal_amount NUMERIC NOT NULL CHECK (goal_amount > 0),
    current_amount NUMERIC DEFAULT 0 CHECK (current_amount >= 0),
    start_date DATE,
    end_date DATE,
    status Status DEFAULT 'active',
    popularity INTEGER DEFAULT 0,
    creator_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE,
    category_id INTEGER REFERENCES category(category_id) ON DELETE SET NULL,
    cover_media_id INTEGER,
    CHECK (end_date IS NULL OR start_date IS NULL OR end_date >= start_date)
);

-- MEDIA TABLE

CREATE TABLE media (
    media_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    file_path TEXT NOT NULL,
    media_type MediaTypes NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE
);

-- Add foreign key constraint from campaign to media
ALTER TABLE campaign
ADD CONSTRAINT fk_campaign_cover_media
FOREIGN KEY (cover_media_id) REFERENCES media(media_id) ON DELETE SET NULL;

-- Add foreign key constraint from user to media
ALTER TABLE "user"
ADD CONSTRAINT fk_user_profile_media
FOREIGN KEY (profile_media_id) REFERENCES media(media_id) ON DELETE SET NULL;

-- Add foreign key constraint from admin to media
ALTER TABLE admin
ADD CONSTRAINT fk_admin_profile_media
FOREIGN KEY (profile_media_id) REFERENCES media(media_id) ON DELETE SET NULL;

-- USER FOLLOWS CAMPAIGN

CREATE TABLE user_follows_campaign (
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, campaign_id)
);

-- CAMPAIGN COLLABORATORS (many-to-many, max 5 per campaign)

CREATE TABLE campaign_collaborators (
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (campaign_id, user_id)
);

-- DONATION TABLE

CREATE TABLE donation (
    donation_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    amount NUMERIC NOT NULL CHECK (amount > 0),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    message TEXT,
    is_anonymous BOOLEAN DEFAULT FALSE,
    is_valid BOOLEAN DEFAULT TRUE,
    donator_id INTEGER REFERENCES "user"(user_id) ON DELETE SET NULL,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE SET NULL
);

-- COMMENT TABLE

CREATE TABLE comment (
    comment_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    text TEXT NOT NULL CHECK (length(text) <= 1000),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE SET NULL,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE
);

-- REPORT TABLE

CREATE TABLE report (
    report_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    reason TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ReportStatus DEFAULT 'open',
    comment_id INTEGER REFERENCES comment(comment_id) ON DELETE CASCADE,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES "user"(user_id)
);

-- CAMPAIGN UPDATE TABLE


CREATE TABLE campaign_update (
    update_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    campaign_id INTEGER REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    author_id INTEGER REFERENCES "user"(user_id) ON DELETE SET NULL
);

-- COLLABORATION REQUEST TABLE

CREATE DOMAIN CollaborationRequestStatus AS TEXT CHECK (VALUE IN ('pending', 'accepted', 'rejected'));

CREATE TABLE collaboration_request (
    request_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    campaign_id INTEGER NOT NULL REFERENCES campaign(campaign_id) ON DELETE CASCADE,
    requester_id INTEGER NOT NULL REFERENCES "user"(user_id) ON DELETE CASCADE,
    message TEXT,
    status CollaborationRequestStatus DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(campaign_id, requester_id)
);

-- NOTIFICATIONS TABLES

CREATE TABLE notification (
    notification_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read BOOLEAN DEFAULT FALSE,
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE
);

CREATE TABLE notification_contribution (
    notification_id INTEGER PRIMARY KEY REFERENCES notification(notification_id) ON DELETE CASCADE,
    donation_id INTEGER REFERENCES donation(donation_id)
);

CREATE TABLE notification_comment (
    notification_id INTEGER PRIMARY KEY REFERENCES notification(notification_id) ON DELETE CASCADE,
    comment_id INTEGER REFERENCES comment(comment_id) ON DELETE CASCADE
);

CREATE TABLE notification_campaign_update (
    notification_id INTEGER PRIMARY KEY REFERENCES notification(notification_id) ON DELETE CASCADE,
    update_id INTEGER REFERENCES campaign_update(update_id) ON DELETE CASCADE
);

CREATE TABLE notification_collaboration_request (
    notification_id INTEGER PRIMARY KEY REFERENCES notification(notification_id) ON DELETE CASCADE,
    request_id INTEGER REFERENCES collaboration_request(request_id) ON DELETE CASCADE
);

-- UNBAN APPEAL TABLE

CREATE TABLE unban_appeal (
    appeal_id INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    reason TEXT NOT NULL,
    status AppealStatus DEFAULT 'pending',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER REFERENCES "user"(user_id) ON DELETE CASCADE
);

-- INDEXES

CREATE INDEX idx_campaign_title ON campaign(title);
CREATE INDEX idx_campaign_category ON campaign(category_id);

CREATE INDEX idx_campaign_fts 
ON campaign 
USING GIN (
  (
    setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
    setweight(to_tsvector('english', coalesce(description, '')), 'B')
  )
);

CREATE INDEX idx_comment_fts 
ON comment 
USING GIN (
  setweight(to_tsvector('english', coalesce(text, '')), 'A')
);

-- TRIGGERS

-- Prevent editing or deleting campaigns that already have donations
CREATE FUNCTION prevent_campaign_edit() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM donation WHERE campaign_id = OLD.campaign_id) THEN
        -- Allow updates only to `current_amount`, `popularity` and `status`
        IF TG_OP = 'UPDATE' AND 
           OLD.title = NEW.title AND 
           OLD.description = NEW.description AND 
           OLD.goal_amount = NEW.goal_amount AND 
           OLD.start_date = NEW.start_date AND 
           OLD.end_date = NEW.end_date AND 
           OLD.creator_id = NEW.creator_id AND 
           OLD.category_id = NEW.category_id THEN
            RETURN NEW;
        END IF;
        RAISE EXCEPTION 'Cannot edit or delete campaign with contributions';
    END IF;
    -- For DELETE operations we must return OLD; for INSERT/UPDATE return NEW.
    IF TG_OP = 'DELETE' THEN
        RETURN OLD;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_campaign_edit
BEFORE UPDATE OR DELETE ON campaign
FOR EACH ROW
EXECUTE FUNCTION prevent_campaign_edit();

-- Prevent users from donating to their own campaign
CREATE FUNCTION prevent_self_donation() RETURNS TRIGGER AS $$
BEGIN
    IF NEW.donator_id = (SELECT creator_id FROM campaign WHERE campaign_id = NEW.campaign_id) THEN
        RAISE EXCEPTION 'Cannot donate to own campaign';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_self_donation
BEFORE INSERT ON donation
FOR EACH ROW
EXECUTE FUNCTION prevent_self_donation();

-- Prevent donations to campaigns that are not active (completed or suspended)
CREATE FUNCTION prevent_donation_to_completed_campaign() RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT status FROM campaign WHERE campaign_id = NEW.campaign_id) <> 'active' THEN
        RAISE EXCEPTION 'Cannot donate to a campaign that is not active';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_prevent_donation_to_completed
BEFORE INSERT ON donation
FOR EACH ROW
EXECUTE FUNCTION prevent_donation_to_completed_campaign();

-- Adjust campaign current_amount and set status to 'completed' when goal reached
CREATE FUNCTION adjust_campaign_amount_and_status() RETURNS TRIGGER AS $$
DECLARE
    delta NUMERIC := 0;
    cid INTEGER;
BEGIN
    -- Recompute the campaign's current_amount from donations to keep a single source of truth
    IF TG_OP = 'INSERT' THEN
        cid := NEW.campaign_id;
    ELSIF TG_OP = 'UPDATE' THEN
        cid := NEW.campaign_id;
    ELSIF TG_OP = 'DELETE' THEN
        cid := OLD.campaign_id;
    ELSE
        RETURN NULL;
    END IF;

    UPDATE campaign
    SET current_amount = COALESCE((SELECT SUM(amount) FROM donation WHERE campaign_id = cid AND (is_valid IS TRUE OR is_valid IS NULL)), 0),
        status = CASE
            WHEN COALESCE((SELECT SUM(amount) FROM donation WHERE campaign_id = cid AND (is_valid IS TRUE OR is_valid IS NULL)), 0) >= goal_amount THEN 'completed'
            ELSE status
        END
    WHERE campaign_id = cid;

    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_adjust_campaign_amount_and_status
AFTER INSERT OR UPDATE OR DELETE ON donation
FOR EACH ROW
EXECUTE FUNCTION adjust_campaign_amount_and_status();

-- Validate campaign dates (end date must be after start)
CREATE FUNCTION validate_campaign_dates() RETURNS TRIGGER AS $$
BEGIN
    IF NEW.end_date IS NOT NULL AND NEW.start_date IS NOT NULL AND NEW.end_date < NEW.start_date THEN
        RAISE EXCEPTION 'End date must be the same as or later than start date';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_validate_campaign_dates
BEFORE INSERT OR UPDATE ON campaign
FOR EACH ROW
EXECUTE FUNCTION validate_campaign_dates();


COMMIT;

-- Ensure campaigns whose end_date is past are marked as 'completed'
CREATE FUNCTION set_campaign_status_completed() RETURNS TRIGGER AS $$
BEGIN
    -- If an end_date exists and is before now(), force status to 'completed'.
    IF NEW.end_date IS NOT NULL AND NEW.end_date < now() THEN
        NEW.status := 'completed';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_set_campaign_status_completed
BEFORE INSERT OR UPDATE ON campaign
FOR EACH ROW
EXECUTE FUNCTION set_campaign_status_completed();
