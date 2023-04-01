import { GetServerSideProps } from 'next/types';
import { InferGetServerSidePropsType } from 'next/types';
import axios from 'axios';

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Styled Component Import
import DataTableWrapper from '../@core/styles/libs/react-datatables'

// ** Demo Components Imports
import SportsTable from '../views/sports/Table'

const Sports = ({data}: InferGetServerSidePropsType<typeof getServerSideProps>) => {

  return (
    <DataTableWrapper>
      <Grid container spacing={6}>
        <Grid item xs={12}>
          <SportsTable sports={data} />
        </Grid>
      </Grid>
    </DataTableWrapper>
  )
}

export const getServerSideProps: GetServerSideProps = async({res}) => {
  try {
    const config = {
      method: 'get',
      maxBodyLength: Infinity,
      url: 'https://api.ps3838.com/v3/sports',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Basic QklBMDAwMzgxVDpQczExMjIzMyoq'
      },
    };

    const response = await axios(config)
    .then(function (response: { data: any; }) {

      return response.data;
    })
    .catch(function (error: any) {
      console.log(error);
    });

    const data = response && response.sports || [];

    return {
      props: {
        data
      },
    }
  } catch {
    res.statusCode = 404;

    return {
      props: {}
    };
  }
};


export default Sports
