
import { ref } from 'vue';

// Create a reactive store
export const useBusinessStore = () => {
  const businessName = ref(localStorage.getItem('business_name') || 'Default Business Name');
  const businessLogo = ref(localStorage.getItem('business_logo') || 'default_logo.png');

  // Method to set business data
  const setBusinessData = (name, logo) => {
    businessName.value = name;
    businessLogo.value = logo;

    // Store in localStorage to persist across sessions
    localStorage.setItem('business_name', name);
    localStorage.setItem('business_logo', logo);
  };

  return {
    businessName,
    businessLogo,
    setBusinessData,
  };
};
