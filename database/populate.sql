SET search_path TO lbaw2532;

-- Sample data script with expanded content for testing (Generated with the help of Claude Sonnet 4.5)

BEGIN;

-- users (password is 'password' for all)
INSERT INTO "user" (email, password, name, bio) VALUES
('alice@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Alice Smith', 'Environmental activist and nature lover.'),
('bruno@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Bruno Johnson', 'Supports social impact causes.'),
('carla@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Carla Brown', 'Animal rights advocate.'),
('david@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'David Wilson', 'Technology enthusiast and volunteer.'),
('emma@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Emma Davis', 'Teacher passionate about education access.'),
('frank@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Frank Miller', 'Documentary filmmaker and storyteller.'),
('grace@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Grace Lee', 'Healthcare worker and community organizer.'),
('henry@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Henry Martinez', 'Artist and creative entrepreneur.'),
('isabel@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Isabel Garcia', 'Software developer and open source contributor.'),
('jack@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Jack Robinson', 'Sports coach and fitness trainer.'),
('kelly@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Kelly White', 'Music teacher and performer.'),
('liam@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Liam Anderson', 'Entrepreneur and startup founder.'),
('mia@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Mia Thompson', 'Writer and journalist covering social issues.'),
('noah@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Noah Clark', 'Engineer working on sustainable technologies.'),
('olivia@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Olivia Walker', 'Veterinarian and animal rescue volunteer.'),
('peter@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Peter Hall', 'Event organizer and community builder.'),
('quinn@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Quinn Young', 'Photographer and visual artist.'),
('rachel@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Rachel King', 'Nonprofit director focused on poverty alleviation.'),
('sam@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Sam Wright', 'Scientist researching climate change solutions.'),
('tina@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Tina Lopez', 'Food blogger and sustainability advocate.');

-- banned user
INSERT INTO "user" (email, password, name, bio, banned) VALUES
('banned@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Banned User', 'This account has been banned.', TRUE);

-- admins
INSERT INTO admin (email, password, name, bio) VALUES
('admin1@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Admin John', 'Cool Admin.'),
('admin2@example.com', '$2y$10$HfzIhGCCaxqyaIdGgjARSuOKAcm1Uy82YfLuNaajn6JrjLWy9Sj/W', 'Admin Rachel', 'Uncool Admin.');

-- categories
INSERT INTO category (name, description) VALUES
('Environment', 'Projects focused on sustainability and environmental protection.'),
('Education', 'Initiatives that promote learning and knowledge sharing.'),
('Health', 'Campaigns supporting healthcare and wellness.'),
('Technology', 'Projects related to innovation and tech development.'),
('Arts & Culture', 'Creative projects such as art, music, film, and performance.'),
('Community', 'Local improvement projects and community-driven initiatives.'),
('Emergency Relief', 'Urgent support for disasters, crises, or personal emergencies.'),
('Entrepreneurship', 'Startups, small businesses, and entrepreneurial ventures.'),
('Animal Welfare', 'Projects protecting animals and supporting humane care.'),
('Sports', 'Funding for sports teams, events, gear, or training.'),
('Social Impact', 'Projects aimed at social change and positive societal outcomes.'),
('Nonprofit', 'Fundraising for nonprofit organizations and charitable causes.'),
('Events', 'Funding for events such as conferences, festivals, or gatherings.'),
('Creative Writing', 'Books, publishing, and writing-focused projects.'),
('Other', 'Projects that do not fall under any existing category.');

-- campaigns
INSERT INTO campaign (title, description, goal_amount, current_amount, start_date, end_date, creator_id, category_id, status) VALUES
('Save the Blue River', 'A campaign to clean up the Blue River and protect local wildlife. We aim to remove tons of plastic waste and restore natural habitats.', 5000, 1500, '2025-10-01', '2026-03-31', 1, 1, 'active'),
('Education for All', 'Helping rural schools get access to learning materials, computers, and books so every child can have quality education.', 8000, 2000, '2025-09-15', '2026-06-30', 2, 2, 'active'),
('Health on Wheels', 'Raising funds to purchase a community ambulance that will serve remote villages without emergency medical services.', 10000, 5000, '2025-08-01', '2026-05-31', 3, 3, 'active'),
('Tech for the Future', 'Funding robotics workshops for young students to inspire the next generation of engineers and innovators.', 7000, 1000, '2025-09-01', '2026-04-30', 4, 4, 'active'),
('Community Garden Project', 'Building a sustainable community garden where families can grow organic vegetables together.', 3000, 2800, '2025-07-01', '2026-02-28', 5, 6, 'active'),
('Rescue Animals in Need', 'Supporting local animal shelters with food, medical supplies, and facility improvements.', 6000, 4500, '2025-06-01', '2026-03-15', 15, 9, 'active'),
('Youth Sports Equipment', 'Purchasing soccer equipment for underprivileged youth teams in our neighborhood.', 2500, 2500, '2025-05-01', '2025-12-31', 10, 10, 'completed'),
('Art Exhibition for Peace', 'Organizing a community art exhibition promoting messages of peace and unity.', 4000, 1200, '2025-08-15', '2026-04-15', 8, 5, 'active'),
('Emergency Flood Relief', 'Urgent fundraiser to help families affected by recent flooding with shelter and supplies.', 15000, 8000, '2025-11-01', '2026-02-28', 7, 7, 'active'),
('Startup Innovation Lab', 'Creating a co-working space for tech startups to collaborate and innovate.', 20000, 5500, '2025-09-01', '2026-08-31', 12, 8, 'active'),
('Musical Instruments for Schools', 'Providing musical instruments to schools that cannot afford music programs.', 5500, 3200, '2025-07-20', '2026-03-20', 11, 5, 'active'),
('Documentary: Ocean Heroes', 'Funding the production of a documentary about ocean conservation efforts worldwide.', 12000, 6000, '2025-10-10', '2026-06-30', 6, 5, 'active'),
('Mobile Clinic Initiative', 'Launching a mobile health clinic to provide free medical checkups in underserved areas.', 18000, 9500, '2025-08-01', '2026-07-31', 7, 3, 'active'),
('Coding Bootcamp for Women', 'Empowering women through free coding classes and tech career mentorship.', 9000, 4200, '2025-09-10', '2026-05-31', 9, 2, 'active'),
('Historic Theater Restoration', 'Restoring our town''s 100-year-old theater to preserve cultural heritage.', 25000, 11000, '2025-06-15', '2026-12-31', 16, 5, 'active'),
('Clean Energy for Villages', 'Installing solar panels in remote villages to provide sustainable electricity.', 30000, 15000, '2025-07-01', '2026-09-30', 14, 1, 'active'),
('Writers Workshop Series', 'Hosting monthly writing workshops to nurture local creative talent.', 3500, 1800, '2025-10-01', '2026-06-30', 13, 14, 'active'),
('Homeless Shelter Expansion', 'Expanding our homeless shelter to accommodate more families during winter.', 40000, 22000, '2025-08-20', '2026-07-31', 18, 11, 'active'),
('Tech Conference 2026', 'Organizing an annual technology conference bringing together industry leaders.', 15000, 7500, '2025-11-01', '2026-07-15', 9, 13, 'active'),
('Community Playground Build', 'Building a safe and accessible playground for children in our neighborhood.', 8000, 8000, '2025-04-01', '2025-11-30', 5, 6, 'completed'),
('Wildlife Photography Book', 'Publishing a photography book showcasing endangered species and their habitats.', 6500, 3100, '2025-09-25', '2026-05-31', 17, 14, 'active'),
('Disaster Preparedness Training', 'Providing emergency response training to community volunteers.', 4500, 2200, '2025-10-15', '2026-03-31', 7, 7, 'active'),
('Urban Farming Initiative', 'Teaching sustainable urban farming techniques to city residents.', 5000, 2900, '2025-08-01', '2026-06-30', 20, 1, 'active'),
('Veterinary Care Fund', 'Providing free veterinary care for pets of low-income families.', 7000, 4100, '2025-07-10', '2026-02-28', 15, 9, 'active'),
('Youth Leadership Camp', 'Organizing a summer leadership camp for high school students.', 6000, 3500, '2025-06-01', '2026-08-31', 2, 2, 'active');

-- user follows campaign
INSERT INTO user_follows_campaign (user_id, campaign_id) VALUES
(2, 1), (3, 1), (4, 1), (5, 1), (6, 1),
(1, 2), (3, 2), (7, 2), (8, 2),
(4, 3), (5, 3), (9, 3), (10, 3), (11, 3),
(1, 4), (2, 4), (6, 4), (12, 4),
(7, 5), (8, 5), (9, 5), (13, 5),
(1, 6), (14, 6), (15, 6), (16, 6),
(10, 7), (17, 7), (18, 7),
(8, 8), (19, 8), (20, 8),
(2, 9), (3, 9), (7, 9), (11, 9),
(9, 10), (12, 10), (13, 10),
(11, 11), (14, 11), (15, 11),
(6, 12), (16, 12), (17, 12),
(7, 13), (18, 13), (19, 13),
(9, 14), (20, 14), (1, 14),
(16, 15), (2, 15), (3, 15),
(14, 16), (4, 16), (5, 16),
(13, 17), (6, 17), (7, 17),
(18, 18), (8, 18), (9, 18),
(9, 19), (10, 19), (11, 19),
(5, 20), (12, 20), (13, 20);

-- donations
INSERT INTO donation (amount, message, is_anonymous, donator_id, campaign_id, date) VALUES
(100, 'Good luck with the project!', FALSE, 2, 1, '2025-10-05'),
(50, 'Amazing cause!', TRUE, 3, 1, '2025-10-08'),
(200, 'Hope you reach your goal soon!', FALSE, 1, 2, '2025-09-20'),
(300, 'Keep up the great work!', FALSE, 4, 3, '2025-08-15'),
(150, 'I believe in this project.', TRUE, 2, 4, '2025-09-10'),
(75, 'Happy to contribute!', FALSE, 5, 1, '2025-10-12'),
(250, 'Every bit helps. Best wishes!', FALSE, 6, 1, '2025-10-18'),
(500, 'Proud to support this initiative.', FALSE, 7, 2, '2025-09-25'),
(100, 'For a better future.', TRUE, 8, 2, '2025-10-01'),
(180, 'Great work on education!', FALSE, 9, 2, '2025-10-10'),
(350, 'Healthcare is so important.', FALSE, 10, 3, '2025-08-20'),
(200, 'Thanks for making a difference.', FALSE, 11, 3, '2025-09-05'),
(150, 'Love what you''re doing!', TRUE, 12, 3, '2025-09-15'),
(90, 'Supporting the next generation.', FALSE, 13, 4, '2025-09-18'),
(120, 'Tech education matters.', FALSE, 14, 4, '2025-09-22'),
(50, 'Small but meaningful.', TRUE, 15, 4, '2025-10-01'),
(300, 'Community gardens are amazing!', FALSE, 16, 5, '2025-07-15'),
(200, 'Love this green initiative.', FALSE, 17, 5, '2025-08-01'),
(500, 'For the animals!', FALSE, 1, 6, '2025-06-10'),
(400, 'Animals deserve better.', FALSE, 14, 6, '2025-07-05'),
(250, 'Keep saving lives!', TRUE, 18, 6, '2025-08-20'),
(150, 'Art brings us together.', FALSE, 1, 8, '2025-08-20'),
(200, 'Peace through art!', FALSE, 19, 8, '2025-09-05'),
(1000, 'Emergency aid is critical.', FALSE, 2, 9, '2025-11-05'),
(500, 'Helping our neighbors.', FALSE, 3, 9, '2025-11-10'),
(750, 'Stay strong!', TRUE, 4, 9, '2025-11-15'),
(300, 'Startups need support.', FALSE, 9, 10, '2025-09-15'),
(400, 'Innovation hub!', FALSE, 13, 10, '2025-10-01'),
(250, 'Music education is vital.', FALSE, 14, 11, '2025-07-25'),
(180, 'Every child deserves music.', FALSE, 1, 11, '2025-08-10'),
(600, 'Ocean conservation matters!', FALSE, 1, 12, '2025-10-15'),
(350, 'Save our seas!', TRUE, 16, 12, '2025-11-01'),
(800, 'Healthcare for all!', FALSE, 3, 13, '2025-08-05'),
(500, 'Mobile clinics are the future.', FALSE, 18, 13, '2025-09-01'),
(300, 'Empowering women in tech!', FALSE, 1, 14, '2025-09-15'),
(250, 'Coding changes lives.', FALSE, 20, 14, '2025-10-05'),
(1000, 'Preserve our history!', FALSE, 4, 15, '2025-07-01'),
(500, 'Love this theater!', FALSE, 2, 15, '2025-08-15'),
(1500, 'Clean energy is the answer.', FALSE, 3, 16, '2025-07-10'),
(800, 'Solar power for all!', TRUE, 4, 16, '2025-08-20'),
(150, 'Writers need support!', FALSE, 11, 17, '2025-10-05'),
(100, 'Keep writing!', FALSE, 6, 17, '2025-10-20'),
(2000, 'Everyone deserves shelter.', FALSE, 1, 18, '2025-09-01'),
(1500, 'Critical work!', FALSE, 8, 18, '2025-10-10'),
(600, 'Excited for the conference!', FALSE, 11, 19, '2025-11-05'),
(400, 'See you there!', FALSE, 10, 19, '2025-11-15'),
(200, 'Beautiful photography!', FALSE, 12, 21, '2025-10-01'),
(150, 'Saving wildlife through art.', TRUE, 1, 21, '2025-10-15'),
(180, 'Preparedness saves lives.', FALSE, 3, 22, '2025-10-20'),
(120, 'Training is essential.', FALSE, 11, 22, '2025-11-01'),
(250, 'Urban farming is cool!', FALSE, 19, 23, '2025-08-10'),
(200, 'Grow your own food!', FALSE, 5, 23, '2025-09-01'),
(300, 'Pets need care too!', FALSE, 3, 24, '2025-07-20'),
(180, 'Veterinary care for all!', TRUE, 1, 24, '2025-08-15'),
(350, 'Future leaders!', FALSE, 1, 25, '2025-06-15'),
(200, 'Investing in youth.', FALSE, 8, 25, '2025-07-10');

-- comments
INSERT INTO comment (text, user_id, campaign_id, date) VALUES
('Love this initiative! Keep it up.', 2, 1, '2025-10-06'),
('When will the next update be posted?', 3, 1, '2025-10-09'),
('Fantastic cause, congratulations!', 1, 2, '2025-09-21'),
('Count me in!', 4, 3, '2025-08-16'),
('Best of luck!', 2, 4, '2025-09-11'),
('This is exactly what our community needs!', 5, 1, '2025-10-14'),
('How can I volunteer to help?', 6, 1, '2025-10-16'),
('Shared this with all my friends!', 7, 2, '2025-09-26'),
('Education is the key to everything.', 8, 2, '2025-10-02'),
('Amazing progress so far!', 9, 2, '2025-10-11'),
('Healthcare access is a human right.', 10, 3, '2025-08-22'),
('Thank you for doing this important work.', 11, 3, '2025-09-07'),
('Can''t wait to see the ambulance in action!', 12, 3, '2025-09-16'),
('STEM education will change the world.', 13, 4, '2025-09-19'),
('My kids would love this program!', 14, 4, '2025-09-23'),
('Robotics is the future!', 15, 4, '2025-10-02'),
('Community gardens bring people together.', 16, 5, '2025-07-16'),
('I want to participate in the planting!', 17, 5, '2025-08-02'),
('Fresh vegetables for everyone!', 5, 5, '2025-08-20'),
('Animals need our help now more than ever.', 1, 6, '2025-06-12'),
('Thank you for caring about animals.', 14, 6, '2025-07-07'),
('Adoption saves lives!', 18, 6, '2025-08-22'),
('Every shelter deserves support.', 19, 6, '2025-09-15'),
('Sports changed my life as a kid.', 10, 7, '2025-05-16'),
('Go local teams!', 17, 7, '2025-06-11'),
('Art has the power to heal.', 8, 8, '2025-08-21'),
('Looking forward to the exhibition!', 19, 8, '2025-09-06'),
('Unity through creativity.', 20, 8, '2025-10-01'),
('Thinking of all those affected by the floods.', 2, 9, '2025-11-06'),
('How can we donate supplies directly?', 3, 9, '2025-11-11'),
('Our community stands together.', 7, 9, '2025-11-16'),
('Startups drive innovation!', 9, 10, '2025-09-16'),
('This co-working space will be a game changer.', 12, 10, '2025-10-02'),
('Entrepreneurship needs support.', 13, 10, '2025-10-15'),
('Music education should be accessible to all.', 11, 11, '2025-07-26'),
('My daughter plays violin thanks to programs like this.', 14, 11, '2025-08-11'),
('Keep the music alive!', 15, 11, '2025-09-05'),
('Ocean conservation is critical.', 6, 12, '2025-10-16'),
('Can''t wait to watch the documentary!', 16, 12, '2025-11-02'),
('Our oceans need heroes.', 17, 12, '2025-11-10'),
('Mobile clinics save lives in rural areas.', 7, 13, '2025-08-06'),
('Preventive care is so important.', 18, 13, '2025-09-02'),
('Healthcare should reach everyone.', 19, 13, '2025-10-05'),
('More women in tech, please!', 9, 14, '2025-09-16'),
('This bootcamp will change lives.', 20, 14, '2025-10-06'),
('Coding is empowerment.', 1, 14, '2025-10-20'),
('Historic buildings tell our story.', 16, 15, '2025-07-02'),
('This theater is part of our heritage.', 2, 15, '2025-08-16'),
('Preservation matters!', 3, 15, '2025-09-10'),
('Solar energy is the future.', 14, 16, '2025-07-11'),
('Clean energy for everyone!', 4, 16, '2025-08-21'),
('Sustainability starts here.', 5, 16, '2025-09-15'),
('Writers need community too.', 13, 17, '2025-10-06'),
('Workshop dates please!', 6, 17, '2025-10-21'),
('Creative writing heals.', 7, 17, '2025-11-05'),
('No one should be without shelter.', 18, 18, '2025-09-02'),
('This expansion is desperately needed.', 8, 18, '2025-10-11'),
('Thank you for your compassion.', 9, 18, '2025-11-01'),
('Tech conferences inspire innovation.', 9, 19, '2025-11-06'),
('Already bought my ticket!', 10, 19, '2025-11-16'),
('Networking opportunities are gold.', 11, 19, '2025-11-20'),
('Playgrounds create childhood memories.', 5, 20, '2025-05-02'),
('Safe spaces for kids to play!', 12, 20, '2025-06-16'),
('Photography captures what words cannot.', 17, 21, '2025-10-02'),
('Wildlife deserves protection.', 1, 21, '2025-10-16'),
('Emergency preparedness is everyone''s responsibility.', 7, 22, '2025-10-21'),
('Training builds confidence.', 11, 22, '2025-11-02'),
('Growing food in the city is revolutionary.', 20, 23, '2025-08-11'),
('Urban farming is sustainable living.', 5, 23, '2025-09-02'),
('Pets are family.', 15, 24, '2025-07-21'),
('Vet care shouldn''t be a luxury.', 1, 24, '2025-08-16'),
('Investing in youth leadership pays dividends.', 2, 25, '2025-06-16'),
('Future leaders are here now.', 8, 25, '2025-07-11');

-- reports
INSERT INTO report (reason, comment_id, user_id, status, date) VALUES
('Offensive comment', 2, 1, 'open', '2025-10-10'),
('Possible spam detected', 5, 3, 'open', '2025-09-12'),
('Inappropriate language', 15, 4, 'resolved', '2025-10-03'),
('Harassment', 28, 6, 'open', '2025-10-02'),
('Misinformation', 42, 9, 'resolved', '2025-10-07');

-- media
INSERT INTO media (file_path, media_type, campaign_id) VALUES
('images/populatecovers/blue_river_cleanup.png', 'image', 1),
('images/populatecovers/education_promo.png', 'image', 2),
('images/populatecovers/health_plan.png', 'image', 3),
('images/populatecovers/robotics_workshop.png', 'image', 4),
('images/populatecovers/community_garden.png', 'image', 5),
('images/populatecovers/animal_rescue.png', 'image', 6),
('images/populatecovers/youth_sports.png', 'image', 7),
('images/populatecovers/art_exhibition.png', 'image', 8),
('images/populatecovers/flood_relief.png', 'image', 9),
('images/populatecovers/startup_lab.png', 'image', 10),
('images/populatecovers/music_instruments.png', 'image', 11),
('images/populatecovers/ocean_documentary.png', 'image', 12),
('images/populatecovers/mobile_clinic.png', 'image', 13),
('images/populatecovers/coding_bootcamp.png', 'image', 14),
('images/populatecovers/theater_restoration.png', 'image', 15),
('images/populatecovers/solar_energy.png', 'image', 16),
('images/populatecovers/writing_workshop.png', 'image', 17),
('images/populatecovers/homeless_shelter.png', 'image', 18),
('images/populatecovers/tech_conference.png', 'image', 19),
('images/populatecovers/playground.png', 'image', 20),
('images/populatecovers/wildlife_photography.png', 'image', 21),
('images/populatecovers/disaster_training.png', 'image', 22),
('images/populatecovers/urban_farming.png', 'image', 23),
('images/populatecovers/veterinary_care.png', 'image', 24),
('images/populatecovers/youth_leadership.png', 'image', 25);

-- campaign cover
UPDATE campaign c
SET cover_media_id = m.media_id
FROM media m
WHERE c.campaign_id = m.campaign_id;

-- campaign updates
INSERT INTO campaign_update (title, content, campaign_id, author_id, date) VALUES
('First Cleanup Completed', 'We successfully cleaned 2 km of the river! Thank you to all our amazing volunteers.', 1, 1, '2025-10-15'),
('New Supplies Delivered', 'Thank you all for your donations! Books and computers arrived today.', 2, 2, '2025-10-01'),
('Ambulance Purchased', 'The new ambulance is on its way to the community! Expected arrival next week.', 3, 3, '2025-09-20'),
('Workshop Launched', 'Our first group of students has started the robotics class. They''re loving it!', 4, 4, '2025-09-25'),
('Garden Plot Ready', 'We''ve prepared the soil and built raised beds. Planting starts this weekend!', 5, 5, '2025-07-20'),
('50 Animals Rescued', 'This month we rescued and rehomed 50 animals. Your support makes this possible!', 6, 15, '2025-08-01'),
('Equipment Arrived', 'All soccer equipment has been delivered to the teams. Season starts next month!', 7, 10, '2025-09-01'),
('Artist Applications Open', 'We''re now accepting submissions from local artists for the peace exhibition.', 8, 8, '2025-09-10'),
('Relief Distribution Begins', 'Started distributing food and supplies to 200 affected families today.', 9, 7, '2025-11-10'),
('First Startups Moving In', 'Three tech startups have already moved into the innovation lab!', 10, 12, '2025-10-15'),
('Instruments Distributed', 'Musical instruments delivered to 5 schools serving 300 students.', 11, 11, '2025-09-05'),
('Filming in Progress', 'Documentary crew is currently filming in the Pacific. Footage looks incredible!', 12, 6, '2025-11-15'),
('Mobile Clinic Operational', 'Our mobile clinic served 150 patients in its first week!', 13, 7, '2025-09-15'),
('First Class Graduated', '20 women completed the coding bootcamp and 15 already have job offers!', 14, 9, '2025-11-01'),
('Restoration Phase 1 Complete', 'Theater facade restoration finished. Moving to interior work next.', 15, 16, '2025-09-01'),
('Solar Panels Installed', 'First village now has 24/7 electricity thanks to solar power!', 16, 14, '2025-09-10'),
('Monthly Workshop Success', 'Last workshop had 40 participants. Next session scheduled for December.', 17, 13, '2025-11-10'),
('Winter Preparations Complete', 'Shelter expansion finished just in time for cold weather. 50 new beds added.', 18, 18, '2025-10-25'),
('Speaker Lineup Announced', 'Confirmed 25 industry leaders as speakers for the tech conference!', 19, 9, '2025-11-25'),
('Playground Grand Opening', 'Playground officially opened! 200 kids attended the opening celebration.', 20, 5, '2025-09-15'),
('Book Preview Released', 'First 20 pages of the wildlife photography book now available online!', 21, 17, '2025-11-01'),
('Training Session 1 Done', '50 volunteers completed emergency response certification this weekend.', 22, 7, '2025-11-08'),
('First Harvest Success', 'Community members harvested 200 kg of vegetables from urban gardens!', 23, 20, '2025-10-20'),
('Free Clinic Day', 'Provided free veterinary care to 30 pets this Saturday. Next clinic in December.', 24, 15, '2025-09-25'),
('Leadership Camp Highlights', 'Students learned conflict resolution, public speaking, and team building!', 25, 2, '2025-08-15');

-- notifications
INSERT INTO notification (content, user_id, is_read, created_at) VALUES
('You received a €100.00 donation from Bruno Johnson on ''Save the Blue River''', 1, FALSE, '2025-10-05 10:30:00'),
('Bruno Johnson commented on your campaign ''Save the Blue River''', 1, FALSE, '2025-10-06 14:20:00'),
('New update posted on ''Health on Wheels'': Ambulance Purchased', 3, FALSE, '2025-09-20 09:15:00'),
('You received a €200.00 donation from Grace Lee on ''Health on Wheels''', 3, TRUE, '2025-09-05 10:15:00'),
('New update posted on ''Save the Blue River'': First Cleanup Completed', 2, FALSE, '2025-10-15 08:00:00'),
('New update posted on ''Community Garden Project'': Garden Plot Ready', 1, FALSE, '2025-07-20 14:10:00'),
('You received a €250.00 donation from an anonymous supporter on ''Rescue Animals in Need''', 15, FALSE, '2025-08-20 11:25:00'),
('Emma Davis commented on your campaign ''Save the Blue River''', 1, FALSE, '2025-10-14 13:40:00'),
('New update posted on ''Emergency Flood Relief'': Relief Distribution Begins', 2, FALSE, '2025-11-10 08:30:00'),
('New collaboration request received.', 5, TRUE, '2025-07-25 15:50:00');

INSERT INTO notification_contribution (notification_id, donation_id) VALUES
(1, 1),
(4, 11),
(7, 21);

INSERT INTO notification_comment (notification_id, comment_id) VALUES
(2, 2),
(8, 6);

INSERT INTO notification_campaign_update (notification_id, update_id) VALUES
(3, 3),
(5, 1),
(6, 5),
(10, 9);

-- collaboration requests
INSERT INTO collaboration_request (campaign_id, requester_id, status, message, created_at) VALUES
(1, 6, 'accepted', 'I have experience with environmental projects and would love to help!', '2025-10-08 10:00:00'),
(2, 8, 'pending', 'I am a teacher and can help coordinate school visits.', '2025-09-28 14:30:00'),
(3, 9, 'accepted', 'I am a nurse and want to contribute to healthcare initiatives.', '2025-08-25 11:15:00'),
(6, 3, 'rejected', 'I can help with social media marketing for the shelter.', '2025-07-15 16:45:00'),
(10, 13, 'pending', 'As a developer, I would like to mentor startup founders.', '2025-10-10 09:30:00');

-- campaign collaborators
INSERT INTO campaign_collaborators (campaign_id, user_id, added_at) VALUES
(1, 6, '2025-10-09 12:00:00'),
(3, 9, '2025-08-26 10:30:00'),
(12, 17, '2025-10-20 15:00:00');

-- unban appeals
INSERT INTO unban_appeal (reason, user_id, status, date) VALUES
('I apologize for my previous behavior and promise to follow community guidelines.', 21, 'pending', '2025-11-01'),
('My comment was misunderstood. I did not mean any offense.', 21, 'pending', '2025-11-10'),
('I have learned from my mistake and request a second chance.', 21, 'rejected', '2025-10-15');

COMMIT;
