-- Add fun_facts column to destinations table
-- This stores an array of 3 interesting/fun facts about the destination that are 100% true

ALTER TABLE destinations 
ADD COLUMN IF NOT EXISTS fun_facts JSONB DEFAULT '[]'::jsonb;

-- Add comment for documentation
COMMENT ON COLUMN destinations.fun_facts IS 'Array of 3 fun/interesting facts about the destination that must be 100% true';
