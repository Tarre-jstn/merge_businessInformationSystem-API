import { reactive } from 'vue';
import axios from 'axios';

const state = reactive({
  businessName: 'Default Business Name',
  businessImage: 'default_logo.png',
  isLoading: true
});

export const useBusinessStore = () => {
  const fetchBusinessData = async () => {
    try {
      const response = await axios.get('/api/business');
      console.log('Business Info Response:', response);
      
      if (response.data && response.data.length > 0) {
        const businessData = response.data[0];
        state.businessName = businessData.business_Name || 'Default Business Name';
        state.businessImage = businessData.business_image || 'default_logo.png';
      } else {
        console.warn('Business data not found or empty');
      }
    } catch (error) {
      console.error('Error fetching business data:', error);
    } finally {
      state.isLoading = false;
    }
  };

  return {
    state,
    fetchBusinessData,
  };
};