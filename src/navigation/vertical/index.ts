// ** Icon imports
import HomeOutline from 'mdi-material-ui/HomeOutline'
import AccountCogOutline from 'mdi-material-ui/AccountCogOutline'
import AlertCircleOutline from 'mdi-material-ui/AlertCircleOutline'

// ** Type import
import { VerticalNavItemsType } from '../../@core/layouts/types'

const navigation = (): VerticalNavItemsType => {
  return [
    {
      title: 'Dashboard',
      icon: HomeOutline,
      path: '/'
    },
    {
      sectionTitle: 'All Events'
    },
    {
      title: 'Sports',
      icon: AlertCircleOutline,
      path: '/sports',
    },
    {
      title: 'Tennis',
      icon: AccountCogOutline,
      path: '/sports/tennis',
    },
  ]
}

export default navigation
