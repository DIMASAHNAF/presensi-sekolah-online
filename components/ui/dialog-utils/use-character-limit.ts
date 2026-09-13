import { useState, ChangeEvent } from "react";

interface UseCharacterLimitProps {
  maxLength: number;
  initialValue?: string;
}

export function useCharacterLimit({
  maxLength,
  initialValue = "",
}: UseCharacterLimitProps) {
  const [value, setValue] = useState(initialValue);
  const [characterCount, setCharacterCount] = useState(initialValue.length);

  const handleChange = (e: ChangeEvent<HTMLTextAreaElement | HTMLInputElement>) => {
    const text = e.target.value;
    if (text.length <= maxLength) {
      setValue(text);
      setCharacterCount(text.length);
    }
  };

  return {
    value,
    characterCount,
    handleChange,
    maxLength,
  };
}
