// ** MUI Imports
import { Typography } from '@mui/material'
import Grid from '@mui/material/Grid'

// ** Styled Component Import
import DataTableWrapper from '../@core/styles/libs/react-datatables/'


const Dashboard = () => {
  return (
    <DataTableWrapper>
      <Grid container spacing={6}>
        <Grid item xs={12}>
          <Typography variant="h5" gutterBottom>
            Welcome to your Dashboard!
          </Typography>
        </Grid>
      </Grid>
    </DataTableWrapper>
  )
}

export default Dashboard
