-- Añadir soporte de video al blog
ALTER TABLE blog_posts ADD COLUMN video_url VARCHAR(500) AFTER imagen_url;
