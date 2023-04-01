import { GetServerSideProps } from 'next/types';
import { InferGetServerSidePropsType } from 'next/types';
import { Typography, Grid, Paper } from '@mui/material';
import { styled } from '@mui/material/styles';
import axios from 'axios';

const Item = styled(Paper)(({ theme }) => ({
    backgroundColor: theme.palette.mode === 'dark' ? '#1A2027' : '#fff',
    ...theme.typography.body2,
    padding: theme.spacing(1),
    textAlign: 'left',
    color: theme.palette.text.secondary,
  }));

// ** Components Imports
import CardLeague from '../../views/sports/Card'

  interface SportsInfo {
    [key: string]: {
        id: number,
        name: string
    }
}

  const sportsInfo: SportsInfo = {
    tennis: { id: 33, name: 'Tennis' }
  }


  const Sport = (props: InferGetServerSidePropsType<typeof getServerSideProps>) => {
    const fixtures = props.data;
    const leagues = fixtures && fixtures.league;

    return (
        <>
        <Typography variant="h5">{props.name} - Fixtures</Typography>
        <Grid container spacing={2}>
        { leagues && leagues.map((row: any) => (
            <Grid key={row.id} item xs={4}>
                <Item><CardLeague data={row} /> </Item>
            </Grid>
        ))}
        </Grid>
        </>
    );
  };

  export const getServerSideProps: GetServerSideProps = async({
    params,
    res
  }) => {
    try {
      let data = {}
      const sportInfo = sportsInfo[params?.slug?.toString() || '']

      if( sportInfo && sportInfo.id ) {

        const config = {
            method: 'get',
            maxBodyLength: Infinity,
            url: 'https://api.ps3838.com/v3/fixtures/?sportId='+sportInfo.id,
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': 'Basic QklBMDAwMzgxVDpQczExMjIzMyoq'
            },
          };

        data = await axios(config)
            .then(function (response: { data: any; }) {
            //console.log(JSON.stringify(response.data));
            return response.data;
            })
            .catch(function (error: any) {
            console.log(error);
            });

      }

    //console.log(JSON.stringify(data));
      return {
        props: {
            name: sportInfo.name,
            data: { name: sportInfo && sportInfo.name || '', ...data }
        },
      }
    } catch {
      res.statusCode = 404;

      return {
        props: {}
      };
    }
  };

  export default Sport;
