-- The query to retrieve total amount spent by each user
SELECT 
    u.username,
    u.email,
    COALESCE(SUM(o.amount), 0) AS total_amount_spent
FROM 
    users u
LEFT JOIN 
    orders o ON u.id = o.user_id
GROUP BY 
    u.id, u.username, u.email
ORDER BY 
    total_amount_spent DESC;