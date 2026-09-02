DELIMITER $$

DROP PROCEDURE IF EXISTS GenerateTransaksiMasal $$

CREATE PROCEDURE GenerateTransaksiMasal()
BEGIN
    DECLARE i INT DEFAULT 1;
    DECLARE p_id INT;
    
    -- Membuat 100.000 baris data transaksi acak
    WHILE i <= 100000 DO
        SELECT id_pelanggan INTO p_id FROM tbl_pelanggan ORDER BY RAND() LIMIT 1;

        INSERT INTO tbl_transaksi (tanggal, id_pelanggan, total_belanja)
        VALUES (
            -- Tanggal acak dalam 365 hari terakhir
            CURRENT_DATE() - INTERVAL FLOOR(RAND() * 365) DAY, 
            
            p_id,                         
            
            -- Total belanja acak antara Rp 10.000 - Rp 1.000.000
            FLOOR(10000 + (RAND() * 990000))                           
        );
        
        SET i = i + 1;
        
        -- Sesekali commit agar tidak berat di memory (opsional)
        IF i % 10000 = 0 THEN
            COMMIT;
        END IF;
    END WHILE;
END $$
DELIMITER ;
